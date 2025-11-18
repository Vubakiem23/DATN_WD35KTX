<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CKEditorUploadController extends Controller
{
    /**
     * Handle inline image uploads from CKEditor 5.
     */
    public function store(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:20000', // max ~5MB
        ]);

        $path = $request->file('upload')->store('uploads', 'public');

        $relativeUrl = Storage::disk('public')->url($path);

        if (Str::startsWith($relativeUrl, ['http://', 'https://'])) {
            $url = $relativeUrl;
        } else {
            $baseUrl = rtrim($request->getSchemeAndHttpHost() . $request->getBaseUrl(), '/');
            $url = $baseUrl . '/' . ltrim($relativeUrl, '/');
        }

        return response()->json([
            'uploaded' => true,
            'url' => $url,
            'path' => $path,
        ]);
    }
}

