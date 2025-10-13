<?php

namespace App\Http\Controllers\Customer;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class WishlistController extends Controller
{

    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with(['product.images'])
            ->latest()
            ->get();

        return view('customer.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            // Remove from wishlist
            $wishlist->delete();
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Product removed from wishlist'
            ]);
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Product added to wishlist'
            ]);
        }
    }

    public function check(Request $request): JsonResponse
    {
        $productIds = $request->input('product_ids', []);
        $userId = Auth::id();

        $wishlisted = Wishlist::where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'wishlisted' => $wishlisted
        ]);
    }
}
