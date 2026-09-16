<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

#[Signature('sharepoint:scan')]
#[Description('Scan SharePoint Input folder and display file information')]
class SharePointScan extends Command
{
    public function handle(): int
    {
        $this->info('Scanning SharePoint Input folder...');
        $this->newLine();

        try {
            // Connect to the configured SharePoint filesystem.
            $disk = Storage::disk('sharepoint');

            // Recursively get all files from Input and its subfolders.
            $files = $disk->allFiles();

            if (empty($files)) {
                $this->warn('No files found in the Input folder.');

                return self::SUCCESS;
            }

            foreach ($files as $file) {

                // Example:
                // Claims/January/claim001.pdf
                $fileName = basename($file);

                // Example:
                // Claims/January
                $folderPath = dirname($file);

                // A file directly inside Input has no subfolder.
                if ($folderPath === '.') {
                    $folderPath = '';
                }

                // Get file size in bytes.
                $size = $disk->size($file);

                // Convert bytes into KB, MB, GB, etc.
                $sizeFormatted = $this->formatBytes($size);

                // Get MIME type.
                $mimeType = $disk->mimeType($file);

                // Get last modified timestamp.
                $lastModified = $disk->lastModified($file);

                // Convert timestamp into readable date/time.
                $lastModifiedFormatted = date(
                    'm/d/Y h:i A',
                    $lastModified
                );

                $this->line(str_repeat('-', 80));

                $this->line("File Name     : {$fileName}");
                $this->line("Folder Path   : {$folderPath}");
                $this->line("File Path     : {$file}");
                $this->line("File Size     : {$sizeFormatted}");
                $this->line("MIME Type     : {$mimeType}");
                $this->line("Last Modified : {$lastModifiedFormatted}");

                // Temporary status for our POC.
                $this->line("Status        : DETECTED");
            }

            $this->line(str_repeat('-', 80));
            $this->newLine();

            $this->info('Total files: ' . count($files));

            return self::SUCCESS;

        } catch (Throwable $e) {

            // Show the error without exposing configuration values.
            $this->error('SharePoint scan failed.');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Convert bytes into a human-readable file size.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $power = floor(log($bytes, 1024));

        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return round($value, 2) . ' ' . $units[$power];
    }
}