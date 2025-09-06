<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'original_filename',
        'original_path',
        'hls_path',
        'thumbnail_path',
        'duration',
        'resolution',
        'file_size',
        'status',
        'processing_log',
        'user_id',
        'category_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the user that owns the video.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the video belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the progress records for this video.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(VideoProgress::class);
    }

    /**
     * Get the HLS playlist URL.
     */
    public function getHlsUrlAttribute(): ?string
    {
        if (!$this->hls_path) {
            return null;
        }

        return Storage::disk('public')->url($this->hls_path . '/playlist.m3u8');
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }

        return Storage::disk('public')->url($this->thumbnail_path);
    }

    /**
     * Get the original file URL.
     */
    public function getOriginalUrlAttribute(): string
    {
        return Storage::url($this->original_path);
    }

    /**
     * Check if video is ready for playback.
     */
    public function isReady(): bool
    {
        $transcodingMethod = env('VIDEO_TRANSCODING_METHOD', 'simple');
        
        switch ($transcodingMethod) {
            case 'ffmpeg':
            case 'external':
                return $this->status === 'completed' && $this->hls_path;
                
            case 'client':
                return $this->status === 'completed' && ($this->hls_path || $this->original_path);
                
            case 'simple':
            default:
                return $this->status === 'completed' && $this->original_path;
        }
    }
    
    /**
     * Get the appropriate video URL based on transcoding method.
     */
    public function getVideoUrlAttribute(): ?string
    {
        $transcodingMethod = env('VIDEO_TRANSCODING_METHOD', 'simple');
        
        switch ($transcodingMethod) {
            case 'ffmpeg':
            case 'external':
            case 'client':
                return $this->hls_url ?: $this->original_url;
                
            case 'simple':
            default:
                return $this->original_url;
        }
    }

    /**
     * Get user's progress for this video.
     */
    public function getUserProgress(int $userId): ?VideoProgress
    {
        return $this->progress()->where('user_id', $userId)->first();
    }

    /**
     * Scope for completed videos.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for processing videos.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope for failed videos.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
