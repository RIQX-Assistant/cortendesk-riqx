<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiqxAd;
use App\Support\RiqxAds;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * RIQX: what the app polls for ADS1 / ADS2 (docs/11-phase-c-ads.md).
 *
 * Both routes are public and tokenless: ads are public content, and the app
 * fetches them whether or not a user is signed in. Throttled in routes/api.php.
 */
class RiqxAdController extends Controller
{
    /** GET /api/riqx/ads?lang=fa|en — the manifest, with ETag / 304. */
    public function manifest(Request $request): JsonResponse|Response
    {
        $body = json_encode(RiqxAds::manifest($request->query('lang')), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $etag = '"'.sha1($body).'"';

        $headers = [
            'ETag' => $etag,
            'Cache-Control' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ];

        $sent = array_map('trim', explode(',', (string) $request->header('If-None-Match', '')));
        if (in_array($etag, $sent, true)) {
            return response('', 304, $headers);
        }

        return new JsonResponse($body, 200, $headers, 0, true);
    }

    /**
     * GET /api/riqx/ads/media/{ad}/{sha256}.{ext} — the bytes. The hash is part
     * of the URL, so a URL never changes meaning and may be cached forever.
     * Range requests (video seeking) come from BinaryFileResponse.
     */
    public function media(int $ad, string $sha256, string $ext): BinaryFileResponse
    {
        $row = RiqxAd::query()->whereKey($ad)->where('sha256', $sha256)->firstOrFail();
        abort_unless(strtolower($ext) === $row->extension(), 404);

        $disk = Storage::disk(RiqxAd::DISK);
        abort_unless($disk->exists($row->filename), 404);

        $response = new BinaryFileResponse($disk->path($row->filename), 200, [
            'Content-Type' => $row->mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ], true, null, false, true);

        return $response;
    }
}
