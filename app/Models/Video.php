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

        return Storage::url($this->thumbnail_path);
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
        return $this->status === 'completed' && $this->hls_path;
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
