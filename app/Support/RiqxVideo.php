<?php

namespace App\Support;

use Illuminate\Support\Facades\Process;

/**
 * RIQX: video ads reach the app as an animated WebP, never as a video.
 *
 * The app showed MP4/WebM through libmpv (media_kit), and on Windows that
 * deadlocked Flutter's raster thread inside libmpv: the window froze until it
 * was killed (docs/11). Ads play muted anyway, so an animated WebP shows the
 * same thing through Flutter's own image decoder, with nothing that can hang.
 */
class RiqxVideo
{
    public const MAX_SECONDS = 60;

    /**
     * Converts $src (MP4/WebM) to an animated WebP at $dst.
     *
     * @return array{seconds: int}|null null when ffmpeg is missing or fails
     */
    public static function toAnimatedWebp(string $src, string $dst): ?array
    {
        $probe = Process::timeout(60)->run([
            'ffprobe', '-v', 'error', '-show_entries', 'format=duration', '-of', 'default=nw=1:nk=1', $src,
        ]);
        $seconds = $probe->successful() ? (float) trim($probe->output()) : 0.0;

        $run = Process::timeout(300)->run([
            'ffmpeg', '-v', 'error', '-y', '-i', $src,
            '-t', (string) self::MAX_SECONDS,
            '-an',
            '-vf', "fps=15,scale='min(1200,iw)':-2:flags=lanczos",
            '-c:v', 'libwebp', '-lossless', '0', '-quality', '75', '-compression_level', '4',
            '-loop', '0',
            $dst,
        ]);

        if (! $run->successful() || ! is_file($dst) || filesize($dst) === 0) {
            @unlink($dst);
            report(new \RuntimeException('RIQX ad video conversion failed: '.trim($run->errorOutput())));

            return null;
        }

        return ['seconds' => (int) max(1, ceil(min($seconds ?: 8, self::MAX_SECONDS)))];
    }
}
