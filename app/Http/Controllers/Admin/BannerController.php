<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Services\ImageProcessingService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.banners.index');
    }

    /**
     * Get banners data for JS Grid
     */
    public function data(Request $request)
    {
        $banners = Banner::query()
            ->when($request->filled('title'), function($query) use ($request) {
                $query->where('title', 'like', '%' . $request->title . '%');
            })
            ->when($request->filled('is_active'), function($query) use ($request) {
                $query->where('is_active', $request->is_active === 'true');
            })
            ->when($request->filled('text_position'), function($query) use ($request) {
                $query->where('text_position', $request->text_position);
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'button_text' => $banner->button_text,
                    'text_position' => $banner->text_position,
                    'is_active' => $banner->is_active,
                    'sort_order' => $banner->sort_order,
                    'image_preview' => $banner->image_url,
                ];
            });

        return response()->json($banners);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ImageProcessingService $imageService)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'button_text' => 'nullable|string|max:100',
            'button_style' => 'nullable|in:primary,secondary,outline',
            'text_color' => 'nullable|string|max:20',
            'background_color' => 'nullable|string|max:20',
            'text_position' => 'required|in:left,center,right',
            'is_active' => 'boolean',
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
            $data = $request->except('image');

            // Handle image upload with optimization
            if ($request->hasFile('image')) {
                $processedData = $imageService->processAndSaveImage(
                    $request->file('image'),
                    'banners',
                    1200,
                    85
                );
                $data['image'] = $processedData['path'];
            }

            $banner = Banner::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Banner berhasil ditambahkan dan dioptimasi ke WebP',
                'data' => $banner
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $banner = Banner::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'description' => $banner->description,
                    'button_text' => $banner->button_text,
                    'button_style' => $banner->button_style,
                    'text_color' => $banner->text_color,
                    'background_color' => $banner->background_color,
                    'text_position' => $banner->text_position,
                    'is_active' => $banner->is_active,
                    'sort_order' => $banner->sort_order,
                    'image_url' => $banner->image_url,
                    'created_at' => $banner->created_at->format('d F Y H:i'),
                    'updated_at' => $banner->updated_at->format('d F Y H:i'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Banner tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id, ImageProcessingService $imageService)
    {
        try {
            $banner = Banner::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
                'button_text' => 'nullable|string|max:100',
                    'button_style' => 'nullable|in:primary,secondary,outline',
                'text_color' => 'nullable|string|max:20',
                'background_color' => 'nullable|string|max:20',
                'text_position' => 'required|in:left,center,right',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->except('image');

            // Handle image upload with optimization
            if ($request->hasFile('image')) {
                // Delete old image
                if ($banner->image && !filter_var($banner->image, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($banner->image);
                }

                $processedData = $imageService->processAndSaveImage(
                    $request->file('image'),
                    'banners',
                    1200,
                    85
                );
                $data['image'] = $processedData['path'];
            }

            $banner->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Banner berhasil diperbarui dan dioptimasi ke WebP',
                'data' => $banner
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->delete();

            return response()->json([
                'success' => true,
                'message' => 'Banner berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}