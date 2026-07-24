<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait MediaUploadingTrait
{
    public function storeMedia(Request $request)
    {
        // Validates file size
        if (request()->has('size')) {
            $this->validate(request(), [
                'file' => 'max:'.request()->input('size') * 1024,
            ]);
        }
        // If width or height is preset - we are validating it as an image
        if (request()->has('width') || request()->has('height')) {
            $this->validate(request(), [
                'file' => sprintf(
                    'image|dimensions:max_width=%s,max_height=%s',
                    request()->input('width', 100000),
                    request()->input('height', 100000)
                ),
            ]);
        }

        $path = storage_path('tmp/uploads');

        try {
            if (! file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }

        $file = $request->file('file');

        $name = uniqid().'_'.trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function serveBase64Image($encodedImage, $path = '')
    {
        $image_parts = [];
        $image_parts = explode(';base64,', $encodedImage);
        $decodedImage = base64_decode($image_parts[1]);
        $imageFormat = 'jpg';
        $uniqueFilename = 'image_'.uniqid().'.'.$imageFormat;
        if ($path) {
            $imageDirectory = storage_path($path);
        } else {
            $imageDirectory = storage_path('app/itemImage/');
        }

        if (! File::isDirectory($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true);
        }
        $imagePath = $imageDirectory.$uniqueFilename;
        File::put($imagePath, $decodedImage);
        $contentType = 'image/png';
        if ($imageFormat === 'jpeg') {
            $contentType = 'image/jpeg';
        } elseif ($imageFormat === 'gif') {
            $contentType = 'image/gif';
        }
        $headers = ['Content-Type' => $contentType];

        return $imagePath;
    }
}
