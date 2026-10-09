<?php

namespace App\Support;

use App\Models\RiqxAd;
use App\Models\Setting;

/**
 * RIQX: the ad manifest the app polls (GET /api/riqx/ads).
 *
 * The manifest is rebuilt per request from the live rows, so a schedule that
 * starts or ends changes it without anyone touching the console. Its ETag is a
 * hash of the body: when nothing changed the app gets a 304 and downloads
 * nothing. "Publish now" bumps riqx_ads_published_at, which changes the body
 * (and so the ETag) even when no row changed, telling every app to re-check.
 */
class RiqxAds
{
    public const PUBLISHED_KEY = 'riqx_ads_published_at';

    /** @return array<string, mixed> */
    public static function manifest(?string $lang = null): array
    {
        $lang = in_array($lang, ['fa', 'en'], true) ? $lang : null;

        $query = RiqxAd::query()->live()->orderBy('sort_order')->orderBy('id');
        if ($lang !== null) {
            $query->whereIn('locale', ['all', $lang]);
        }

        $slots = array_fill_keys(RiqxAd::SLOTS, []);
        foreach ($query->get() as $ad) {
            $slots[$ad->slot][] = [
                'id' => $ad->id,
                'type' => $ad->media_type,
                'mime' => $ad->mime,
                'url' => $ad->mediaUrl(),
                'sha256' => $ad->sha256,
                'size' => $ad->size,
                'width' => $ad->width,
                'height' => $ad->height,
                'duration_ms' => $ad->media_type === 'video' ? null : $ad->duration_seconds * 1000,
                'link' => $ad->link_url,
                'title' => $ad->title,
            ];
        }

        return [
            'published_at' => Setting::get(self::PUBLISHED_KEY),
            'poll_seconds' => max(60, (int) config('riqx.ads_poll_seconds', 600)),
            'slots' => $slots,
        ];
    }

    public static function publishNow(): void
    {
        Setting::put(self::PUBLISHED_KEY, now()->toIso8601String());
    }
}
