<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Common\UploadRequest;

class UploadController extends Controller
{
    /**
     * Handle file upload securely.
     */
    public function store(UploadRequest $request)
    {
        $request->validated();

        $file = $request->file('file');

        // Security: Check for executable signature patterns even if mime matches
        $handle = fopen($file->getRealPath(), 'rb');
        $header = fread($handle, 4);
        fclose($handle);

        // Block common executable headers (MZ, ELF, PHAR)
        if (str_starts_with($header, 'MZ') || str_contains($header, 'ELF') || str_contains($header, '<?php')) {
            return ResponseHelper::error('Malicious file content detected.', 422);
        }

        // Generate secure filename using random string (UUID)
        // Use extension() derived from server-side mime check, not client-side original extension
        $filename = generate_uuid() . '.' . $file->extension();

        // Store in 'uploads' directory
        $path = $file->storeAs('uploads', $filename, 'public');

        return ResponseHelper::success([
            'path' => $path,
            'url' => url('storage/' . $path),
            'original_name' => strip_tags($file->getClientOriginalName()),
            'mime_type' => $file->getMimeType(),
        ], 'File uploaded successfully.');
    }
}
