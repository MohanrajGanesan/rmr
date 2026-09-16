<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SharePointFileController extends Controller
{
    /**
     * Get all files recursively from the SharePoint Input folder.
     */
    public function index(): JsonResponse
    {
        try {
            $disk = Storage::disk('sharepoint');

            // Get all files inside Input and its subfolders.
            $files = $disk->allFiles();

            $results = [];

            foreach ($files as $file) {
                // Example:
                // Claims/January/Subfolder/claim001.pdf

                $fileName = basename($file);

                $folderPath = dirname($file);

                if ($folderPath === '.') {
                    $folderPath = '';
                }

                // Immediate parent folder.
                $folderName = $folderPath !== ''
                    ? basename($folderPath)
                    : 'Input';

                $fileSize = $disk->size($file);

                $mimeType = $disk->mimeType($file);

                $lastModifiedTimestamp = $disk->lastModified($file);

                $results[] = [
                    'folder_name' => $folderName,
                    'file_name' => $fileName,
                    'folder_path' => $folderPath,
                    'file_size' => $this->formatBytes($fileSize),
                    'mime_type' => $mimeType,
                    'last_modified' => date(
                        'Y-m-d H:i:s',
                        $lastModifiedTimestamp
                    ),
                    'status' => 'DETECTED',
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => count($results),
            ]);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve SharePoint files.',
            ], 500);
        }
    }

    /**
     * Convert bytes into a readable file size.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $power = floor(log($bytes, 1024));

        $power = min(
            $power,
            count($units) - 1
        );

        $value = $bytes / (1024 ** $power);

        return round($value, 2) . ' ' . $units[$power];
    }
}