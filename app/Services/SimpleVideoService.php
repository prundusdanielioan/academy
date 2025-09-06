<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SimpleVideoService
{
    /**
     * Process video without transcoding (direct MP4 streaming).
     */
    public function process(Video $video): bool
    {
        try {
            $video->update(['status' => 'processing']);
            
            // Generate thumbnail using PHP-GD (no FFmpeg needed)
            $thumbnailPath = $this->generateThumbnailFromVideo($video);
            
            // Get basic metadata
            $metadata = $this->getBasicMetadata($video);
            
            // Update video record
            $video->update([
                'thumbnail_path' => $thumbnailPath,
                'duration' => $metadata['duration'] ?? null,
                'resolution' => $metadata['resolution'] ?? null,
                'file_size' => $metadata['file_size'] ?? null,
                'status' => 'completed',
                'processing_log' => 'Simple processing completed (no transcoding)'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Simple video processing failed for video ' . $video->id, [
                'error' => $e->getMessage(),
                'video_id' => $video->id
            ]);
            
            $video->update([
                'status' => 'failed',
                'processing_log' => 'Simple processing failed: ' . $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Generate a thumbnail by extracting a frame from the video.
     */
    private function generateThumbnailFromVideo(Video $video): string
    {
        $thumbnailPath = 'thumbnails/' . $video->id . '.jpg';
        $thumbnailDir = storage_path('app/public/thumbnails');
        
        if (!file_exists($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }
        
        $videoPath = Storage::disk('public')->path($video->original_path);
        $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);
        
        // Try to extract a frame from the video using FFmpeg
        if ($this->extractFrameWithFFmpeg($videoPath, $thumbnailFullPath)) {
            return $thumbnailPath;
        }
        
        // If FFmpeg fails, try using PHP's built-in video processing
        if ($this->extractFrameWithPHP($videoPath, $thumbnailFullPath)) {
            return $thumbnailPath;
        }
        
        // Fallback to a nice placeholder if all else fails
        return $this->generatePlaceholderThumbnail($video, $thumbnailPath);
    }
    
    /**
     * Extract frame using FFmpeg (if available).
     */
    private function extractFrameWithFFmpeg(string $videoPath, string $thumbnailPath): bool
    {
        // Check if FFmpeg is available
        exec('ffmpeg -version 2>&1', $output, $returnCode);
        if ($returnCode !== 0) {
            return false;
        }
        
        // Extract frame at 1 second (or 10% of video duration, whichever is smaller)
        $command = sprintf(
            'ffmpeg -i "%s" -ss 00:00:01 -vframes 1 -q:v 2 -y "%s" 2>&1',
            $videoPath,
            $thumbnailPath
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($thumbnailPath) && filesize($thumbnailPath) > 0) {
            // Resize to standard thumbnail size
            $this->resizeThumbnail($thumbnailPath, 320, 180);
            return true;
        }
        
        return false;
    }
    
    /**
     * Extract frame using PHP (fallback method).
     */
    private function extractFrameWithPHP(string $videoPath, string $thumbnailPath): bool
    {
        // This is a simplified approach - in reality, PHP can't directly extract video frames
        // without external libraries. This would require additional packages like:
        // - php-ffmpeg/php-ffmpeg
        // - or other video processing libraries
        
        // For now, return false to use placeholder
        return false;
    }
    
    /**
     * Generate a nice placeholder thumbnail as fallback.
     */
    private function generatePlaceholderThumbnail(Video $video, string $thumbnailPath): string
    {
        $width = 320;
        $height = 180;
        $image = imagecreate($width, $height);
        
        // Create a gradient background
        for ($i = 0; $i < $height; $i++) {
            $progress = $i / $height;
            $r = (int)(40 + $progress * 30);
            $g = (int)(80 + $progress * 20);
            $b = (int)(120 + $progress * 40);
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }
        
        // Add play button
        $playButtonBg = imagecolorallocate($image, 0, 0, 0);
        $playButtonColor = imagecolorallocate($image, 255, 255, 255);
        
        imagefilledellipse($image, $width/2, $height/2, 60, 60, $playButtonBg);
        imageellipse($image, $width/2, $height/2, 60, 60, $playButtonColor);
        
        // Draw play triangle
        $trianglePoints = [
            (int)($width/2 - 15), (int)($height/2 - 10),
            (int)($width/2 - 15), (int)($height/2 + 10),
            (int)($width/2 + 15), (int)($height/2)
        ];
        imagefilledpolygon($image, $trianglePoints, $playButtonColor);
        
        // Add video title
        $title = strlen($video->title) > 20 ? substr($video->title, 0, 17) . '...' : $video->title;
        $textColor = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 3, 10, $height - 30, $title, $textColor);
        
        imagejpeg($image, storage_path('app/public/' . $thumbnailPath), 85);
        imagedestroy($image);
        
        return $thumbnailPath;
    }
    
    /**
     * Resize thumbnail to standard dimensions.
     */
    private function resizeThumbnail(string $thumbnailPath, int $width, int $height): void
    {
        $image = imagecreatefromjpeg($thumbnailPath);
        if (!$image) {
            return;
        }
        
        $resized = imagecreatetruecolor($width, $height);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
        
        imagejpeg($resized, $thumbnailPath, 85);
        imagedestroy($image);
        imagedestroy($resized);
    }
    
    /**
     * Get basic metadata without FFprobe.
     */
    private function getBasicMetadata(Video $video): array
    {
        $filePath = Storage::disk('public')->path($video->original_path);
        
        return [
            'file_size' => file_exists($filePath) ? filesize($filePath) : null,
            'duration' => null, // Would need FFprobe for this
            'resolution' => null, // Would need FFprobe for this
        ];
    }
    
    /**
     * Check if video is ready for streaming.
     */
    public function isReady(Video $video): bool
    {
        return $video->status === 'completed' && $video->original_path;
    }
}
