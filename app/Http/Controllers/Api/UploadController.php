<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'no_file'], 400);
        }

        $file = $request->file('file');
        $path = $file->store('uploads', 'public');
        $upload = Upload::create(['path' => $path, 'disk' => 'public', 'user_id' => optional($request->user())->id]);
        $url = Storage::disk('public')->url($path);
        return response()->json(['url' => $url, 'path' => $path]);
    }
}
