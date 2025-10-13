<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the homepage with products and categories
     */
    public function index()
    {
        // Get active banners for slider
        $banners = Banner::active()->get();

        // Get featured products (limit 8 for display)
        $featuredProducts = Product::with(['images' => function($query) {
            $query->where('is_primary', true)->orderBy('is_primary', 'desc');
        }])
        ->where('is_active', true)
        ->where('is_featured', true)
        ->orderBy('created_at', 'desc')
        ->limit(8)
        ->get();

        // If no featured products, get latest products
        if ($featuredProducts->count() < 8) {
            $additionalProducts = Product::with(['images' => function($query) {
                $query->where('is_primary', true)->orderBy('is_primary', 'desc');
            }])
            ->where('is_active', true)
            ->where('is_featured', false)
            ->orderBy('created_at', 'desc')
            ->limit(8 - $featuredProducts->count())
            ->get();

            $featuredProducts = $featuredProducts->concat($additionalProducts);
        }

        // Get main categories for homepage display (different from global nav categories)
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('home.index', compact('banners', 'featuredProducts', 'categories'));
    }

    /**
     * Display the shop page with products
     */
    public function shop(Request $request)
    {
        $query = Product::with(['images' => function($q) {
            $q->where('is_primary', true)->orderBy('is_primary', 'desc');
        }, 'category'])
        ->where('is_active', true);

        // Filter by wishlist
        if ($request->has('wishlist') && $request->wishlist && Auth::check()) {
            $wishlistProductIds = Wishlist::where('user_id', Auth::id())->pluck('product_id');
            $query->whereIn('id', $wishlistProductIds);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', '');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                // Default: most popular (featured first, then by creation date)
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12);

        // Categories for shop page filtering (use global categories)
        return view('home.shop', compact('products'));
    }


    /**
     * Display product detail
     */
    public function productDetail($id)
    {
        $product = Product::with(['images', 'category'])->findOrFail($id);

        // Get related products from same category
        $relatedProducts = Product::with(['images' => function($query) {
            $query->where('is_primary', true)->orderBy('is_primary', 'desc');
        }])
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('is_active', true)
        ->limit(4)
        ->get();

        return view('home.product-detail', compact('product', 'relatedProducts'));
    }
}