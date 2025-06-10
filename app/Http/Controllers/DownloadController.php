<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Download a file from storage
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function download(Request $request): StreamedResponse
    {
        $path = $request->get('path');
        $filename = $request->get('filename');

        $this->validateDownloadRequest($path);

        // Use custom filename if provided, otherwise use original
        $downloadName = $filename ?: basename($path);

        return Storage::disk('local')->download($path, $downloadName);
    }

    /**
     * Validate the download request
     *
     * @param string|null $path
     * @return void
     */
    protected function validateDownloadRequest(?string $path): void
    {
        // Check if path is provided
        if (!$path) {
            abort(404, 'No file specified');
        }

        // Prevent directory traversal attacks
        if ($this->isPathTraversal($path)) {
            abort(403, 'Invalid file path');
        }

        // Check if file exists
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found');
        }
    }

    /**
     * Check if path contains directory traversal attempts
     *
     * @param string $path
     * @return bool
     */
    protected function isPathTraversal(string $path): bool
    {
        return Str::contains($path, '..') ||
            Str::contains($path, '//') ||
            Str::contains($path, '\\') ||
            Str::startsWith($path, '/') ||
            Str::contains($path, ':');
    }

    /**
     * Download a protected file with additional security checks
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function protectedDownload(Request $request): StreamedResponse
    {
        // Additional authorization checks can be added here
        // For example: check if user has permission to download this specific file type

        $path = $request->get('path');

        // You can add additional checks based on file type or location
        if (Str::startsWith($path, 'invoices/')) {
            // Check if user has permission to download invoices
            // $this->authorize('download-invoices');
        }

        return $this->download($request);
    }
}
