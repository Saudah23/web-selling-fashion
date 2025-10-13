<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SimulateAllPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:simulate-all-pending {--limit=10 : Maximum orders to simulate} {--older-than=5 : Orders older than X minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate payment success for all pending orders (sandbox testing only)';

    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        parent::__construct();
        $this->midtransService = $midtransService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $olderThanMinutes = $this->option('older-than');

        $this->info("🎯 Simulating payment success for pending orders...");
        $this->line("📋 Limit: {$limit} orders");
        $this->line("⏰ Older than: {$olderThanMinutes} minutes");
        $this->newLine();

        // Get pending orders
        $pendingOrders = Order::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes($olderThanMinutes))
            ->with('paymentTransaction')
            ->limit($limit)
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending orders found matching criteria.');
            return 0;
        }

        $this->info("Found {$pendingOrders->count()} pending orders to simulate.");

        if (!$this->confirm('Do you want to proceed with simulation?')) {
            $this->info('Simulation cancelled.');
            return 0;
        }

        $simulated = 0;
        $errors = 0;

        foreach ($pendingOrders as $order) {
            if (!$order->paymentTransaction) {
                $this->warn("  ⚠️  {$order->order_number}: No payment transaction. Skipping.");
                continue;
            }

            $this->line("  🔄 Simulating: {$order->order_number}");

            try {
                // Simulate Midtrans notification for settlement
                $fakeNotification = [
                    'order_id' => $order->order_number,
                    'transaction_status' => 'settlement',
                    'fraud_status' => 'accept',
                    'transaction_id' => $order->paymentTransaction->transaction_id,
                    'payment_type' => $order->paymentTransaction->payment_type ?? 'bank_transfer',
                    'settlement_time' => now()->toISOString(),
                    '_bulk_simulation' => true
                ];

                $result = $this->midtransService->handleNotification($fakeNotification);

                if ($result) {
                    $this->info("  ✅ {$order->order_number}: Payment simulated successfully");
                    $simulated++;
                } else {
                    $this->error("  ❌ {$order->order_number}: Simulation failed");
                    $errors++;
                }

            } catch (\Exception $e) {
                $this->error("  ❌ {$order->order_number}: " . $e->getMessage());
                $errors++;

                Log::error('Bulk payment simulation failed', [
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->newLine();
        $this->info("🎉 Bulk simulation completed:");
        $this->info("  ✅ Simulated: {$simulated}");
        $this->info("  ❌ Errors: {$errors}");
        $this->info("  📊 Total processed: {$pendingOrders->count()}");

        Log::info('Bulk payment simulation completed', [
            'simulated' => $simulated,
            'errors' => $errors,
            'total' => $pendingOrders->count()
        ]);

        return 0;
    }
}
