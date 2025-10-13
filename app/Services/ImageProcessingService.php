<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageProcessingService
{
    /**
     * Process and save single optimized image as WebP
     */
    public function processAndSaveImage(UploadedFile $file, string $directory = 'products', int $maxWidth = 1200, int $quality = 85): array
    {
        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $filenameWithoutExt = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $uniqueId = time() . '_' . Str::random(8);
        $filename = $filenameWithoutExt . '_' . $uniqueId . '.webp';

        // Load and process image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Resize if larger than max width
        if ($originalWidth > $maxWidth) {
            $image = $image->scale(width: $maxWidth);
        }

        // Save as WebP with optimization
        $path = $directory . '/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Ensure directory exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save with WebP format and quality optimization
        $image->toWebp(quality: $quality)->save($fullPath);

        return [
            'original_name' => $originalName,
            'original_size' => $file->getSize(),
            'original_mime' => $file->getMimeType(),
            'filename' => $filename,
            'path' => $path,
            'url' => 'storage/' . $path,
            'width' => $image->width(),
            'height' => $image->height(),
            'file_size' => filesize($fullPath),
            'metadata' => [
                'original_width' => $originalWidth,
                'original_height' => $originalHeight,
                'format' => 'webp',
                'quality' => $quality,
                'compressed' => $originalWidth > $maxWidth
            ]
        ];
    }

    /**
     * Delete processed image files
     */
    public function deleteProcessedImage(array $metadata): bool
    {
        try {
            // Handle both old and new metadata structure
            if (isset($metadata['path'])) {
                // New single file structure
                if (Storage::disk('public')->exists($metadata['path'])) {
                    Storage::disk('public')->delete($metadata['path']);
                }
            } elseif (isset($metadata['processed_images'])) {
                // Old multiple files structure (backward compatibility)
                foreach ($metadata['processed_images'] as $imageData) {
                    if (isset($imageData['path']) && Storage::disk('public')->exists($imageData['path'])) {
                        Storage::disk('public')->delete($imageData['path']);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete processed image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Optimize single image for web (convenience method)
     */
    public function optimizeForWeb(UploadedFile $file, int $maxWidth = 1200, int $quality = 85): array
    {
        return $this->processAndSaveImage($file, 'products', $maxWidth, $quality);
    }
}