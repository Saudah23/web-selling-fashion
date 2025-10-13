<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check-pending {--limit=10 : Maximum number of orders to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pending payment status from Midtrans and update orders accordingly';

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

        $this->info("Checking pending payments (limit: {$limit})...");

        // Get pending orders created in the last 24 hours
        $pendingOrders = Order::where('status', 'pending')
            ->where('created_at', '>=', now()->subDay())
            ->with('paymentTransaction')
            ->limit($limit)
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending orders found.');
            return 0;
        }

        $this->info("Found {$pendingOrders->count()} pending orders to check.");

        $updated = 0;
        $errors = 0;

        foreach ($pendingOrders as $order) {
            if (!$order->paymentTransaction) {
                $this->warn("Order {$order->order_number} has no payment transaction. Skipping.");
                continue;
            }

            $this->line("Checking order: {$order->order_number}");

            try {
                $result = $this->midtransService->checkAndUpdatePaymentStatus($order->order_number);

                if ($result['success']) {
                    $this->info("✓ Order {$order->order_number} updated to: {$result['order_status']}");
                    $updated++;
                } else {
                    $this->error("✗ Order {$order->order_number}: {$result['message']}");
                    $errors++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Order {$order->order_number}: " . $e->getMessage());
                $errors++;

                Log::error('CheckPendingPayments command failed for order', [
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->newLine();
        $this->info("Payment check completed:");
        $this->info("- Orders checked: {$pendingOrders->count()}");
        $this->info("- Orders updated: {$updated}");
        $this->info("- Errors: {$errors}");

        return 0;
    }
}
