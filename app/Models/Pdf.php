<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Pdf extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'original_filename',
        'file_path',
        'file_size',
        'page_count',
        'status',
        'processing_log',
        'is_public',
        'user_id',
        'category_id',
    ];

    protected $casts = [
        'status' => 'string',
        'is_public' => 'boolean',
        'page_count' => 'integer',
    ];

    /**
     * Get the user that owns the PDF.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the PDF belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the PDF file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get the PDF download URL.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('pdfs.download', $this->id);
    }

    /**
     * Check if PDF is ready for viewing/downloading.
     */
    public function isReady(): bool
    {
        return $this->status === 'completed' && $this->file_path;
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope for completed PDFs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for processing PDFs.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope for failed PDFs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for public PDFs.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
