<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Daftar penilaian/rating dari pelanggan.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'order'])->latest();

        if ($rating = $request->get('rating')) {
            $query->where('rating', $rating);
        }

        if ($search = $request->get('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Review::count(),
            'average' => round(Review::avg('rating') ?? 0, 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Hapus penilaian (moderasi).
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Penilaian berhasil dihapus.');
    }
}
