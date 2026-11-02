<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientDownload;
use Illuminate\Http\JsonResponse;

/**
 * RIQX: what the app polls to learn about a new version (docs/12-app-update.md).
 *
 * Public and tokenless like /downloads, whose published rows it lists. Only ids
 * go out, never URLs: the app builds the link from its own API address, so a
 * row can never send an installer download to another host. Throttled in
 * routes/api.php.
 */
class RiqxUpdateController extends Controller
{
    /** "1.5.0-r6.3": the only shape the app compares. */
    private const VERSION = '/^\d+\.\d+\.\d+-r\d+(\.\d+)*$/';

    /** GET /api/riqx/update */
    public function manifest(): JsonResponse
    {
        $files = ClientDownload::published()->ordered()->get()
            ->filter(fn (ClientDownload $d) => preg_match(self::VERSION, (string) $d->version) === 1
                && preg_match('/^[0-9a-f]{64}$/', (string) $d->sha256) === 1)
            ->map(fn (ClientDownload $d) => [
                'id' => $d->id,
                'version' => $d->version,
                'platform' => $d->platform,
                'arch' => $d->arch,
                'ext' => strtolower(pathinfo($d->original_name, PATHINFO_EXTENSION)),
                'size' => $d->size,
                'sha256' => $d->sha256,
                'signature' => $d->update_signature,
            ])
            ->values();

        return response()->json(['files' => $files], 200, [
            'Cache-Control' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ], JSON_UNESCAPED_SLASHES);
    }
}
