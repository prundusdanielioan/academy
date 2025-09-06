<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\SimpleVideoService;
use Illuminate\Console\Command;

class RegenerateThumbnails extends Command
{
    protected $signature = 'video:regenerate-thumbnails {--all : Regenerate all thumbnails}';
    protected $description = 'Regenerate thumbnails for videos using the simple transcoding method';

    public function handle(SimpleVideoService $simpleVideoService)
    {
        $this->info('Regenerating thumbnails...');
        
        $query = Video::where('status', 'completed');
        
        if (!$this->option('all')) {
            // Only regenerate thumbnails that are likely to be simple placeholders
            $query->where(function($q) {
                $q->whereNull('thumbnail_path')
                  ->orWhere('thumbnail_path', 'like', 'thumbnails/%');
            });
        }
        
        $videos = $query->get();
        
        if ($videos->isEmpty()) {
            $this->info('No videos found to regenerate thumbnails for.');
            return;
        }
        
        $this->info("Found {$videos->count()} videos to process.");
        
        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($videos as $video) {
            try {
                // Use reflection to access the private method
                $reflection = new \ReflectionClass($simpleVideoService);
                $method = $reflection->getMethod('generateThumbnailFromVideo');
                $method->setAccessible(true);
                
                $thumbnailPath = $method->invoke($simpleVideoService, $video);
                $video->update(['thumbnail_path' => $thumbnailPath]);
                
                $successCount++;
            } catch (\Exception $e) {
                $this->error("Failed to regenerate thumbnail for video {$video->id}: " . $e->getMessage());
                $failCount++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("✅ Successfully regenerated {$successCount} thumbnails");
        if ($failCount > 0) {
            $this->warn("❌ Failed to regenerate {$failCount} thumbnails");
        }
    }
}
