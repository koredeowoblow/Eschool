<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Handle file upload securely.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt|max:10240', // Max 10MB, Safe types only
        ]);

        $file = $request->file('file');

        // Generate secure filename using UUID
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store in 'public/uploads' directory (requires php artisan storage:link)
        // or just 'uploads' if using a different disk.
        // Using 'public' disk for general access is common via /storage/
        $path = $file->storeAs('uploads', $filename, 'public');

        return get_success_response([
            'path' => $path,
            'url' => url('storage/' . $path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
        ], 'File uploaded successfully.');
    }
}
