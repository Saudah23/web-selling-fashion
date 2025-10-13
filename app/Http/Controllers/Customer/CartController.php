<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display cart page
     */
    public function index()
    {
        $cartItems = Cart::with(['product.images'])
            ->forUser(Auth::id())
            ->get();

        $total = $cartItems->sum('subtotal');

        return view('home.cart', compact('cartItems', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is active
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available'
            ], 400);
        }

        // Check stock availability
        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available. Only ' . $product->stock_quantity . ' items left.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartItem) {
                // Update existing cart item
                $newQuantity = $cartItem->quantity + $request->quantity;

                // Check total quantity against stock
                if ($product->stock_quantity < $newQuantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more items. You already have ' . $cartItem->quantity . ' in cart. Only ' . $product->stock_quantity . ' available.'
                    ], 400);
                }

                $cartItem->update(['quantity' => $newQuantity]);
                $message = 'Cart updated successfully';
            } else {
                // Create new cart item
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity
                ]);
                $message = 'Product added to cart successfully';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $this->getCartCount()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart'
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->with('product')
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        // Check stock availability
        if ($cartItem->product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available. Only ' . $cartItem->product->stock_quantity . ' items left.'
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'subtotal' => $cartItem->subtotal,
            'cart_count' => $this->getCartCount()
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id'
        ]);

        $cartItem = Cart::where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $this->getCartCount()
        ]);
    }

    /**
     * Get cart count for current user
     */
    public function getCount()
    {
        return response()->json([
            'success' => true,
            'count' => $this->getCartCount()
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0
        ]);
    }

    /**
     * Helper method to get cart count
     */
    private function getCartCount()
    {
        return Cart::where('user_id', Auth::id())->sum('quantity');
    }
}