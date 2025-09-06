<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalTranscoderService
{
    private string $processingServerUrl;
    
    public function __construct()
    {
        $this->processingServerUrl = env('PROCESSING_SERVER_URL', 'http://localhost:3000');
    }
    
    /**
     * Transcode video using external processing server.
     */
    public function transcode(Video $video): bool
    {
        try {
            $video->update(['status' => 'processing']);
            
            // Upload video to processing server
            $uploadResponse = $this->uploadVideo($video);
            
            if (!$uploadResponse['success']) {
                throw new \Exception('Failed to upload video to processing server');
            }
            
            // Start transcoding job
            $transcodeResponse = $this->startTranscoding($video, $uploadResponse['upload_id']);
            
            if (!$transcodeResponse['success']) {
                throw new \Exception('Failed to start transcoding job');
            }
            
            // Store job ID for status checking
            $video->update([
                'processing_job_id' => $transcodeResponse['job_id'],
                'processing_log' => 'Transcoding started on external server'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('External transcoding failed for video ' . $video->id, [
                'error' => $e->getMessage(),
                'video_id' => $video->id
            ]);
            
            $video->update([
                'status' => 'failed',
                'processing_log' => 'External transcoding failed: ' . $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Upload video to processing server.
     */
    private function uploadVideo(Video $video): array
    {
        $videoPath = storage_path('app/public/' . $video->original_path);
        
        $response = Http::attach(
            'video', file_get_contents($videoPath), $video->original_filename
        )->post($this->processingServerUrl . '/upload', [
            'video_id' => $video->id,
            'title' => $video->title
        ]);
        
        return $response->json();
    }
    
    /**
     * Start transcoding job on processing server.
     */
    private function startTranscoding(Video $video, string $uploadId): array
    {
        $response = Http::post($this->processingServerUrl . '/transcode', [
            'upload_id' => $uploadId,
            'video_id' => $video->id,
            'callback_url' => route('processing.callback')
        ]);
        
        return $response->json();
    }
    
    /**
     * Check transcoding status.
     */
    public function checkStatus(Video $video): array
    {
        if (!$video->processing_job_id) {
            return ['status' => 'unknown'];
        }
        
        $response = Http::get($this->processingServerUrl . '/status/' . $video->processing_job_id);
        return $response->json();
    }
}
