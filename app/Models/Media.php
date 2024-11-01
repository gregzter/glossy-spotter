<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $table = 'media'; // Laravel utilisera 'media' au lieu de 'medias'

    protected $fillable = [
        'spot_id',
        'original_path',
        'thumbnail_path',
        'upscaled_path',
        'type',
        'status',
        'mime_type',
        'file_size',
        'dimensions',
        'file_hash'
    ];

    protected $casts = [
        'spot_id' => 'integer',
        'original_path' => 'string',
        'thumbnail_path' => 'string',
        'upscaled_path' => 'string',
        'type' => 'string',
        'status' => 'string',
        'mime_type' => 'string',
        'file_size' => 'integer',
        'dimensions' => 'string'
    ];

    // Relations
    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeError($query)
    {
        return $query->where('status', 'error');
    }

    // Accesseurs
    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->type === 'video';
    }

    public function getIsProcessingAttribute(): bool
    {
        return $this->status === 'processing';
    }

    public function getIsReadyAttribute(): bool
    {
        return $this->status === 'ready';
    }

    public function getHasErrorAttribute(): bool
    {
        return $this->status === 'error';
    }

    public function getHasUpscaledVersionAttribute(): bool
    {
        return !empty($this->upscaled_path);
    }

    // Méthodes utilitaires
    public function getDimensionsArray(): array
    {
        [$width, $height] = explode('x', $this->dimensions);
        return [
            'width' => (int) $width,
            'height' => (int) $height
        ];
    }

    public function markAsReady(): bool
    {
        return $this->update(['status' => 'ready']);
    }

    public function markAsError(): bool
    {
        return $this->update(['status' => 'error']);
    }
}
