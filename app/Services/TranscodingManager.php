<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Log;

class TranscodingManager
{
    private string $method;
    
    public function __construct()
    {
        $this->method = env('VIDEO_TRANSCODING_METHOD', 'simple');
    }
    
    /**
     * Process video using the configured method.
     */
    public function process(Video $video): bool
    {
        Log::info('Processing video with method: ' . $this->method, ['video_id' => $video->id]);
        
        switch ($this->method) {
            case 'ffmpeg':
                return app(HlsTranscoderService::class)->transcode($video);
                
            case 'external':
                return app(ExternalTranscoderService::class)->transcode($video);
                
            case 'client':
                // For client-side processing, just mark as ready for client processing
                $video->update([
                    'status' => 'ready_for_client_processing',
                    'processing_log' => 'Ready for client-side transcoding'
                ]);
                return true;
                
            case 'simple':
            default:
                return app(SimpleVideoService::class)->process($video);
        }
    }
    
    /**
     * Check if the configured method is available.
     */
    public function isMethodAvailable(): bool
    {
        switch ($this->method) {
            case 'ffmpeg':
                return app(HlsTranscoderService::class)->isFfmpegAvailable();
                
            case 'external':
                return !empty(env('PROCESSING_SERVER_URL'));
                
            case 'client':
            case 'simple':
            default:
                return true;
        }
    }
    
    /**
     * Get available transcoding methods.
     */
    public function getAvailableMethods(): array
    {
        $methods = [];
        
        // Check FFmpeg
        if (app(HlsTranscoderService::class)->isFfmpegAvailable()) {
            $methods['ffmpeg'] = 'FFmpeg (Local)';
        }
        
        // Check external server
        if (!empty(env('PROCESSING_SERVER_URL'))) {
            $methods['external'] = 'External Processing Server';
        }
        
        // Always available
        $methods['client'] = 'Client-Side Processing';
        $methods['simple'] = 'Simple MP4 Streaming';
        
        return $methods;
    }
}
