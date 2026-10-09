<?php

namespace App\Support;

/**
 * RIQX: brand files live in public/riqx-brand. On the server that folder is a
 * read-only mount of /opt/riqx-brand, so replacing a file there changes the
 * logo on the next page load. The file's mtime in the URL busts browser caches.
 */
class Brand
{
    public static function url(string $file): string
    {
        $mtime = @filemtime(public_path('riqx-brand/'.$file));

        return asset('riqx-brand/'.$file).'?v='.($mtime ?: config('cortendesk.api_version'));
    }

    public static function isRtl(): bool
    {
        return app()->getLocale() === 'fa';
    }

    public static function dir(): string
    {
        return self::isRtl() ? 'rtl' : 'ltr';
    }
}
