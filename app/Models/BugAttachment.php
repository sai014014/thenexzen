<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BugAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bug_id',
        'original_name',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'thumbnail_path'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationship
    public function bug()
    {
        return $this->belongsTo(Bug::class);
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }
        return $this->file_url;
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileIconAttribute()
    {
        $mimeType = $this->mime_type;
        
        if (str_starts_with($mimeType, 'image/')) {
            return 'fas fa-image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'fas fa-video';
        } elseif ($mimeType === 'application/pdf') {
            return 'fas fa-file-pdf';
        } elseif (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ])) {
            return 'fas fa-file-word';
        } elseif (in_array($mimeType, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])) {
            return 'fas fa-file-excel';
        } elseif (in_array($mimeType, [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ])) {
            return 'fas fa-file-powerpoint';
        } else {
            return 'fas fa-file';
        }
    }

    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isVideo()
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    // Helper methods
    public function deleteFile()
    {
        try {
            // Delete main file
            if (Storage::disk('public')->exists($this->file_path)) {
                Storage::disk('public')->delete($this->file_path);
            }
            
            // Delete thumbnail if exists
            if ($this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path)) {
                Storage::disk('public')->delete($this->thumbnail_path);
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error deleting attachment files: ' . $e->getMessage(), [
                'attachment_id' => $this->id,
                'file_path' => $this->file_path,
                'thumbnail_path' => $this->thumbnail_path
            ]);
            return false;
        }
    }
}
