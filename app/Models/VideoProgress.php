<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoProgress extends Model
{
    use HasFactory;

    protected $table = 'video_progress';

    protected $fillable = [
        'video_id',
        'user_id',
        'progress_percentage',
        'current_time',
        'total_time',
        'is_completed',
        'last_watched_at',
    ];

    protected $casts = [
        'progress_percentage' => 'float',
        'current_time' => 'integer',
        'total_time' => 'integer',
        'is_completed' => 'boolean',
        'last_watched_at' => 'datetime',
    ];

    /**
     * Get the video that this progress belongs to.
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Get the user that this progress belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update progress with current time and total time.
     */
    public function updateProgress(int $currentTime, int $totalTime): void
    {
        $this->current_time = $currentTime;
        $this->total_time = $totalTime;
        $this->progress_percentage = $totalTime > 0 ? ($currentTime / $totalTime) * 100 : 0;
        $this->is_completed = $this->progress_percentage >= 90; // Consider completed at 90%
        $this->last_watched_at = now();
        $this->save();
    }

    /**
     * Mark as completed.
     */
    public function markAsCompleted(): void
    {
        $this->is_completed = true;
        $this->progress_percentage = 100;
        $this->last_watched_at = now();
        $this->save();
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedCurrentTimeAttribute(): string
    {
        return $this->formatTime($this->current_time);
    }

    /**
     * Get formatted total time.
     */
    public function getFormattedTotalTimeAttribute(): string
    {
        return $this->formatTime($this->total_time);
    }

    /**
     * Format time in seconds to HH:MM:SS.
     */
    private function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }
}
