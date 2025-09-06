<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Log;

class ClientSideTranscoderService
{
    /**
     * Handle client-side transcoding completion.
     */
    public function handleClientTranscoding(Video $video, array $transcodedData): bool
    {
        try {
            // Save HLS files uploaded by client
            $hlsPath = $this->saveHlsFiles($video, $transcodedData['hls_files']);
            $thumbnailPath = $this->saveThumbnail($video, $transcodedData['thumbnail']);
            
            // Update video record
            $video->update([
                'hls_path' => $hlsPath,
                'thumbnail_path' => $thumbnailPath,
                'duration' => $transcodedData['duration'] ?? null,
                'resolution' => $transcodedData['resolution'] ?? null,
                'file_size' => $transcodedData['file_size'] ?? null,
                'status' => 'completed',
                'processing_log' => 'Client-side transcoding completed'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Client-side transcoding failed for video ' . $video->id, [
                'error' => $e->getMessage(),
                'video_id' => $video->id
            ]);
            
            $video->update([
                'status' => 'failed',
                'processing_log' => 'Client-side transcoding failed: ' . $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Save HLS files uploaded by client.
     */
    private function saveHlsFiles(Video $video, array $hlsFiles): string
    {
        $hlsPath = 'hls/' . $video->id;
        $hlsDir = storage_path('app/public/' . $hlsPath);
        
        if (!file_exists($hlsDir)) {
            mkdir($hlsDir, 0755, true);
        }
        
        foreach ($hlsFiles as $filename => $content) {
            file_put_contents($hlsDir . '/' . $filename, base64_decode($content));
        }
        
        return $hlsPath;
    }
    
    /**
     * Save thumbnail uploaded by client.
     */
    private function saveThumbnail(Video $video, string $thumbnailData): string
    {
        $thumbnailPath = 'thumbnails/' . $video->id . '.jpg';
        $thumbnailDir = storage_path('app/public/thumbnails');
        
        if (!file_exists($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }
        
        file_put_contents(
            storage_path('app/public/' . $thumbnailPath),
            base64_decode($thumbnailData)
        );
        
        return $thumbnailPath;
    }
}
