<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Class CleanupOldLabels
 *
 * Artisan command responsible for cleaning up old PDF label files
 * stored on the private "labels" filesystem disk.
 *
 * This command is intended to be executed by the Laravel scheduler
 * and helps prevent disk space accumulation by deleting label PDFs
 * older than a defined expiration time.
 *
 * Default behavior:
 * - Scans all files in the "labels" disk
 * - Deletes files older than 30 minutes
 *
 * @package App\Console\Commands
 */
class CleanupOldLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'labels:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old label PDF files';

    /**
     * Execute the console command.
     *
     * Iterates over all files in the private "labels" storage disk and
     * deletes any PDF files whose last modification time exceeds the
     * configured expiration threshold.
     *
     * @return int Exit code indicating command execution status
     */
    public function handle(): int
    {
        $disk = Storage::disk('labels');

        if (! $disk->exists('/')) {
            return Command::SUCCESS;
        }

        $files = $disk->files();
        $deleteBefore = now()->subMinutes(30)->timestamp;

        $deleted = 0;

        foreach ($files as $file) {
            if ($disk->lastModified($file) < $deleteBefore) {
                $disk->delete($file);
                $deleted++;
            }
        }

        $this->info("Deleted {$deleted} old label PDFs.");

        return Command::SUCCESS;
    }
}
