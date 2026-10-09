<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * RIQX: one ad in the RIQX app. The bytes live on the `riqx_ads` disk and are
 * served only by Api\RiqxAdController under an immutable /{id}/{sha256} URL.
 */
#[Fillable([
    'slot', 'title', 'media_type', 'filename', 'original_name', 'mime', 'size', 'sha256',
    'width', 'height', 'duration_seconds', 'link_url', 'locale', 'starts_at', 'ends_at',
    'is_active', 'sort_order', 'uploaded_by',
])]
class RiqxAd extends Model
{
    public const DISK = 'riqx_ads';

    public const SLOTS = ['ads1', 'ads2'];

    public const LOCALES = ['all', 'fa', 'en'];

    /** Extension => [media_type, mime]. WebP may be animated; the app decodes both. */
    public const TYPES = [
        'png' => ['image', 'image/png'],
        'jpg' => ['image', 'image/jpeg'],
        'jpeg' => ['image', 'image/jpeg'],
        'webp' => ['image', 'image/webp'],
        'gif' => ['gif', 'image/gif'],
        'mp4' => ['video', 'video/mp4'],
        'webm' => ['video', 'video/webm'],
    ];

    /**
     * Recommended artwork size per slot (shown in the console), at 2x for
     * high-DPI screens. ADS1 is drawn 200 px wide in the app's left column
     * (3:4); ADS2 spans the main area above the status bar (6:1).
     */
    public const SIZES = [
        'ads1' => [480, 640],
        'ads2' => [1200, 200],
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration_seconds' => 'integer',
            'sort_order' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $ad) {
            Storage::disk(self::DISK)->delete($ad->filename);
        });
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('slot')->orderBy('sort_order')->orderBy('id');
    }

    /** Active and inside its schedule right now. */
    public function scopeLive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', $now));
    }

    /** "live", "scheduled", "ended" or "off" — for the console list. */
    public function state(): string
    {
        if (! $this->is_active) {
            return 'off';
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'scheduled';
        }
        if ($this->ends_at && ! $this->ends_at->isFuture()) {
            return 'ended';
        }

        return 'live';
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
    }

    public function mediaUrl(): string
    {
        return url('/api/riqx/ads/media/'.$this->id.'/'.$this->sha256.'.'.$this->extension());
    }
}
