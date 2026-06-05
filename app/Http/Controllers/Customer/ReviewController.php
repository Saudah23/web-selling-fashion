<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Simpan / perbarui penilaian pelanggan untuk sebuah produk pada pesanan yang sudah diterima.
     */
    public function store(Request $request, string $orderNumber)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Silakan pilih jumlah bintang.',
            'rating.min' => 'Penilaian minimal 1 bintang.',
            'rating.max' => 'Penilaian maksimal 5 bintang.',
        ]);

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items')
            ->firstOrFail();

        // Hanya bisa menilai jika pesanan sudah diterima
        if ($order->status !== 'delivered') {
            return redirect()->back()
                ->with('error', 'Penilaian hanya dapat diberikan setelah pesanan diterima.');
        }

        // Pastikan produk memang ada di dalam pesanan ini
        $productInOrder = $order->items->contains('product_id', (int) $validated['product_id']);
        if (!$productInOrder) {
            return redirect()->back()
                ->with('error', 'Produk tidak ditemukan dalam pesanan ini.');
        }

        Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $validated['product_id'],
                'order_id' => $order->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return redirect()->back()
            ->with('success', 'Terima kasih! Penilaian Anda telah disimpan.');
    }
}
