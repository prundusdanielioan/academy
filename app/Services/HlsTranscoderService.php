<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class HlsTranscoderService
{
    /**
     * Transcode video to HLS format.
     */
    public function transcode(Video $video): bool
    {
        try {
            $video->update(['status' => 'processing']);
            
            $originalPath = Storage::disk('public')->path($video->original_path);
            $hlsOutputPath = $this->getHlsOutputPath($video);
            
            // Create HLS output directory in public disk
            Storage::disk('public')->makeDirectory($hlsOutputPath);
            
            // Transcode to HLS
            $success = $this->executeFfmpegTranscode($originalPath, $hlsOutputPath);
            
            if (!$success) {
                throw new \Exception('FFmpeg transcoding failed');
            }
            
            // Generate thumbnail
            $thumbnailPath = $this->generateThumbnail($originalPath, $video);
            
            // Get video metadata
            $metadata = $this->getVideoMetadata($originalPath);
            
            // Update video record
            $video->update([
                'hls_path' => $hlsOutputPath,
                'thumbnail_path' => $thumbnailPath,
                'duration' => $metadata['duration'] ?? null,
                'resolution' => $metadata['resolution'] ?? null,
                'file_size' => $metadata['file_size'] ?? null,
                'status' => 'completed',
                'processing_log' => 'Transcoding completed successfully'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('HLS transcoding failed for video ' . $video->id, [
                'error' => $e->getMessage(),
                'video_id' => $video->id
            ]);
            
            $video->update([
                'status' => 'failed',
                'processing_log' => 'Transcoding failed: ' . $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Execute FFmpeg transcoding command.
     */
    private function executeFfmpegTranscode(string $inputPath, string $outputPath): bool
    {
        $outputDir = Storage::disk('public')->path($outputPath);
        
        // FFmpeg command for HLS transcoding with multiple qualities
        $command = sprintf(
            'ffmpeg -i "%s" -c:v libx264 -c:a aac -hls_time 6 -hls_list_size 0 -hls_segment_filename "%s/segment_%%03d.ts" -f hls "%s/playlist.m3u8" 2>&1',
            $inputPath,
            $outputDir,
            $outputDir
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            Log::error('FFmpeg command failed', [
                'command' => $command,
                'output' => $output,
                'return_code' => $returnCode
            ]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate thumbnail from video.
     */
    private function generateThumbnail(string $videoPath, Video $video): string
    {
        $thumbnailPath = 'thumbnails/' . $video->id . '.jpg';
        $thumbnailFullPath = Storage::path($thumbnailPath);
        
        // Create thumbnails directory
        Storage::makeDirectory('thumbnails');
        
        // Extract thumbnail at 5 seconds
        $command = sprintf(
            'ffmpeg -i "%s" -ss 00:00:05 -vframes 1 -q:v 2 "%s" 2>&1',
            $videoPath,
            $thumbnailFullPath
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            Log::warning('Thumbnail generation failed', [
                'video_id' => $video->id,
                'output' => $output
            ]);
            return '';
        }
        
        // Resize thumbnail to standard size
        $manager = new ImageManager(new Driver());
        $image = $manager->read($thumbnailFullPath);
        $image->resize(320, 180);
        $image->save($thumbnailFullPath);
        
        return $thumbnailPath;
    }
    
    /**
     * Get video metadata using FFprobe.
     */
    private function getVideoMetadata(string $videoPath): array
    {
        $command = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
            $videoPath
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            return [];
        }
        
        $metadata = json_decode(implode('', $output), true);
        
        if (!$metadata) {
            return [];
        }
        
        $videoStream = null;
        foreach ($metadata['streams'] ?? [] as $stream) {
            if ($stream['codec_type'] === 'video') {
                $videoStream = $stream;
                break;
            }
        }
        
        return [
            'duration' => $metadata['format']['duration'] ?? null,
            'resolution' => $videoStream ? ($videoStream['width'] . 'x' . $videoStream['height']) : null,
            'file_size' => $metadata['format']['size'] ?? null,
        ];
    }
    
    /**
     * Get HLS output path for video.
     */
    private function getHlsOutputPath(Video $video): string
    {
        return 'hls/' . $video->id;
    }
    
    /**
     * Check if FFmpeg is available.
     */
    public function isFfmpegAvailable(): bool
    {
        exec('ffmpeg -version 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }
    
    /**
     * Get FFmpeg version.
     */
    public function getFfmpegVersion(): ?string
    {
        if (!$this->isFfmpegAvailable()) {
            return null;
        }
        
        exec('ffmpeg -version 2>&1', $output);
        return $output[0] ?? null;
    }
}
