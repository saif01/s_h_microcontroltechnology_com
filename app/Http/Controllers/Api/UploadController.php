<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            'folder' => 'nullable|string|max:255', // Optional folder name
        ]);

        try {
            $file = $request->file('image');
            $folder = $request->input('folder', 'products');
            $prefix = $request->input('prefix', ''); // Product name prefix
            
            // Get file info before moving
            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            
            // Generate filename with prefix if provided
            $filenameBase = '';
            if (!empty($prefix)) {
                // Sanitize prefix: remove special chars, convert to lowercase, replace spaces with hyphens
                $sanitizedPrefix = Str::slug($prefix);
                $filenameBase = $sanitizedPrefix . '-';
            }
            $filename = $filenameBase . Str::random(20) . '.' . $extension;
            
            // Create folder in public directory if it doesn't exist
            $publicPath = public_path('uploads/' . $folder);
            if (!file_exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true);
            }
            
            // Move file to public/uploads/{folder}/
            $file->move($publicPath, $filename);
            
            // Get relative path and full URL
            $relativePath = 'uploads/' . $folder . '/' . $filename;
            $url = asset($relativePath);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $relativePath,
                'filename' => $filename,
                'size' => $fileSize,
                'mime_type' => $mimeType,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadMultipleImages(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'folder' => 'nullable|string|max:255',
        ]);

        try {
            $files = $request->file('images');
            $folder = $request->input('folder', 'products');
            $prefix = $request->input('prefix', ''); // Product name prefix
            $uploaded = [];

            // Create folder in public directory if it doesn't exist
            $publicPath = public_path('uploads/' . $folder);
            if (!file_exists($publicPath)) {
                File::makeDirectory($publicPath, 0755, true);
            }

            // Generate filename base with prefix if provided
            $filenameBase = '';
            if (!empty($prefix)) {
                // Sanitize prefix: remove special chars, convert to lowercase, replace spaces with hyphens
                $sanitizedPrefix = Str::slug($prefix);
                $filenameBase = $sanitizedPrefix . '-';
            }

            foreach ($files as $index => $file) {
                // Get file info before moving
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                
                // Generate filename with prefix and index for uniqueness
                $filename = $filenameBase . Str::random(20) . '-' . ($index + 1) . '.' . $extension;
                
                // Move file to public/uploads/{folder}/
                $file->move($publicPath, $filename);
                
                // Get relative path and full URL
                $relativePath = 'uploads/' . $folder . '/' . $filename;
                $url = asset($relativePath);

                $uploaded[] = [
                    'url' => $url,
                    'path' => $relativePath,
                    'filename' => $filename,
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                ];
            }

            return response()->json([
                'success' => true,
                'images' => $uploaded,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            // Remove 'uploads/' or 'storage/' prefix if present in the path
            $path = str_replace(['uploads/', 'storage/'], '', $request->path);
            $fullPath = public_path('uploads/' . $path);
            
            if (file_exists($fullPath)) {
                unlink($fullPath);
                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }
}

