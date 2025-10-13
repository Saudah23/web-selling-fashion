<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.products.index');
    }

    /**
     * Get products data for JS Grid
     */
    public function data(Request $request)
    {
        $products = Product::with('category')
            ->when($request->filled('name'), function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->when($request->filled('sku'), function($query) use ($request) {
                $query->where('sku', 'like', '%' . $request->sku . '%');
            })
            ->when($request->filled('category_id'), function($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('is_active'), function($query) use ($request) {
                $query->where('is_active', $request->is_active === 'true');
            })
            ->when($request->filled('is_featured'), function($query) use ($request) {
                $query->where('is_featured', $request->is_featured === 'true');
            })
            ->when($request->filled('low_stock'), function($query) use ($request) {
                if ($request->low_stock === 'true') {
                    $query->lowStock();
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category_name' => $product->category ? $product->category->name : '-',
                    'price' => $product->formatted_price,
                    'stock_quantity' => $product->stock_quantity,
                    'is_low_stock' => $product->is_low_stock,
                    'is_active' => $product->is_active,
                    'is_featured' => $product->is_featured,
                    'sort_order' => $product->sort_order,
                ];
            });

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
            'images' => 'nullable|array',
            'attributes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $productData = $request->all();

            // Handle price conversion
            if (isset($productData['price'])) {
                $productData['price'] = str_replace(['Rp', '.', ' '], '', $productData['price']);
            }
            if (isset($productData['compare_price'])) {
                $productData['compare_price'] = str_replace(['Rp', '.', ' '], '', $productData['compare_price']);
            }

            $product = Product::create($productData);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->load('category')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
            'images' => 'nullable|array',
            'attributes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $productData = $request->all();

            // Handle price conversion
            if (isset($productData['price'])) {
                $productData['price'] = str_replace(['Rp', '.', ' '], '', $productData['price']);
            }
            if (isset($productData['compare_price'])) {
                $productData['compare_price'] = str_replace(['Rp', '.', ' '], '', $productData['compare_price']);
            }

            $product->update($productData);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'data' => $product->load('category')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for select dropdown
     */
    public function categories()
    {
        // Only return subcategories (child categories) for product assignment
        $categories = Category::active()
            ->whereNotNull('parent_id') // Only subcategories
            ->with('parent:id,name') // Include parent for better context
            ->ordered()
            ->get(['id', 'name', 'parent_id'])
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->parent->name . ' → ' . $category->name
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::active()->count(),
            'featured_products' => Product::featured()->count(),
            'low_stock_products' => Product::lowStock()->count(),
            'total_stock_value' => Product::active()->sum('stock_quantity'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Bulk update stock quantities
     */
    public function bulkUpdateStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.stock_quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updatedCount = 0;
            foreach ($request->products as $productData) {
                Product::where('id', $productData['id'])
                    ->update(['stock_quantity' => $productData['stock_quantity']]);
                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil memperbarui stok {$updatedCount} produk"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload product images
     */
    public function uploadImages(Request $request, Product $product, ImageProcessingService $imageService)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'alt_texts' => 'nullable|array',
            'alt_texts.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $uploadedImages = [];
            $altTexts = $request->input('alt_texts', []);
            $currentImageCount = $product->images()->count();

            foreach ($request->file('images') as $index => $imageFile) {
                // Process image to WebP with optimization
                $processedData = $imageService->optimizeForWeb($imageFile, 1200, 85);

                // Create product image record with processed data
                $productImage = ProductImage::create([
                    'product_id' => $product->id,
                    'filename' => $processedData['filename'],
                    'path' => $processedData['path'],
                    'url' => $processedData['url'],
                    'alt_text' => $altTexts[$index] ?? $processedData['original_name'],
                    'is_primary' => $currentImageCount === 0 && $index === 0, // First image becomes primary
                    'sort_order' => $currentImageCount + $index,
                    'file_size' => $processedData['file_size'],
                    'mime_type' => 'image/webp',
                    'metadata' => $processedData['metadata'],
                ]);

                $uploadedImages[] = $productImage;
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedImages) . ' gambar berhasil diupload dan dioptimasi ke WebP',
                'data' => collect($uploadedImages)->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'filename' => $image->filename,
                        'url' => $image->full_url,
                        'alt_text' => $image->alt_text,
                        'is_primary' => $image->is_primary,
                        'file_size' => $image->formatted_file_size,
                        'metadata' => $image->metadata,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product images
     */
    public function getImages(Product $product)
    {
        $images = $product->images()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $images
        ]);
    }

    /**
     * Update image details
     */
    public function updateImage(Request $request, Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Gambar tidak ditemukan untuk produk ini'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'alt_text' => 'nullable|string|max:255',
            'is_primary' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image->update($request->only(['alt_text', 'is_primary', 'sort_order']));

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil diperbarui',
                'data' => $image->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product image
     */
    public function deleteImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Gambar tidak ditemukan untuk produk ini'
            ], 404);
        }

        try {
            $image->delete(); // This will also delete the physical file via model event

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set image as primary
     */
    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Gambar tidak ditemukan untuk produk ini'
            ], 404);
        }

        try {
            $image->update(['is_primary' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Gambar berhasil dijadikan gambar utama',
                'data' => $image->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengatur gambar utama: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder product images
     */
    public function reorderImages(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'image_orders' => 'required|array',
            'image_orders.*.id' => 'required|exists:product_images,id',
            'image_orders.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->image_orders as $orderData) {
                ProductImage::where('id', $orderData['id'])
                    ->where('product_id', $product->id)
                    ->update(['sort_order' => $orderData['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Urutan gambar berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan gambar: ' . $e->getMessage()
            ], 500);
        }
    }
}
