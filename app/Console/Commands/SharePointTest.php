<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

#[Signature('sharepoint:test')]
#[Description('Test SharePoint connection and list files from the Input folder')]
class SharePointTest extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Connecting to SharePoint...');

        try {
            $disk = Storage::disk('sharepoint');

            $files = $disk->files();

            $this->newLine();
            $this->info('Files found:');
            $this->newLine();

            if (empty($files)) {
                $this->warn('No files found in the Input folder.');

                return self::SUCCESS;
            }

            foreach ($files as $file) {
                $this->line("- {$file}");
            }

            $this->newLine();
            $this->info('Total files: ' . count($files));

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error('SharePoint connection failed.');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}