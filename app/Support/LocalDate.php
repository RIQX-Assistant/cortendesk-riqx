<?php

namespace App\Support;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use IntlDateFormatter;

/**
 * RIQX: every date the console prints goes through here, so Persian pages show
 * the Solar Hijri (Jalali) calendar and every page shows the display timezone
 * (RIQX_DISPLAY_TIMEZONE, default Asia/Tehran) instead of raw UTC.
 *
 * Each method returns null for a null input, so `LocalDate::date($x) ?? '—'`
 * keeps working the way `$x?->format('Y-m-d') ?? '—'` did.
 */
class LocalDate
{
    /** Gregorian (PHP date) and Jalali (ICU) pattern for each style. */
    private const STYLES = [
        'date' => ['Y-m-d', 'yyyy/MM/dd'],
        'dateTime' => ['Y-m-d H:i', 'yyyy/MM/dd HH:mm'],
        'dateTimeSeconds' => ['Y-m-d H:i:s', 'yyyy/MM/dd HH:mm:ss'],
        'long' => ['M j, Y g:i A', 'd MMMM yyyy، HH:mm'],
        'short' => ['M j', 'd MMMM'],
        'time' => ['H:i', 'HH:mm'],
    ];

    /** 2026-10-09 / ۱۴۰۵/۰۷/۱۷ */
    public static function date(mixed $value): ?string
    {
        return self::render($value, 'date');
    }

    /** 2026-10-09 19:30 / ۱۴۰۵/۰۷/۱۷ ۱۹:۳۰ */
    public static function dateTime(mixed $value): ?string
    {
        return self::render($value, 'dateTime');
    }

    /** 2026-10-09 19:30:05 / ۱۴۰۵/۰۷/۱۷ ۱۹:۳۰:۰۵ */
    public static function dateTimeSeconds(mixed $value): ?string
    {
        return self::render($value, 'dateTimeSeconds');
    }

    /** Oct 9, 2026 7:30 PM / ۱۷ مهر ۱۴۰۵، ۱۹:۳۰ */
    public static function long(mixed $value): ?string
    {
        return self::render($value, 'long');
    }

    /** Oct 9 / ۱۷ مهر */
    public static function short(mixed $value): ?string
    {
        return self::render($value, 'short');
    }

    /** 19:30 / ۱۹:۳۰ */
    public static function time(mixed $value): ?string
    {
        return self::render($value, 'time');
    }

    public static function timezone(): string
    {
        return (string) config('riqx.display_timezone', 'Asia/Tehran');
    }

    public static function isJalali(): bool
    {
        return app()->getLocale() === 'fa';
    }

    private static function render(mixed $value, string $style): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $at = $value instanceof CarbonInterface
            ? $value->copy()
            : ($value instanceof DateTimeInterface ? Carbon::instance($value) : Carbon::parse((string) $value));
        $at = $at->setTimezone(self::timezone());

        [$gregorian, $jalali] = self::STYLES[$style];

        if (! self::isJalali() || ! class_exists(IntlDateFormatter::class)) {
            return $at->format($gregorian);
        }

        $formatter = new IntlDateFormatter(
            'fa_IR@calendar=persian',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            self::timezone(),
            IntlDateFormatter::TRADITIONAL,
            $jalali,
        );

        $out = $formatter->format($at->toDateTimeImmutable());

        return $out === false ? $at->format($gregorian) : $out;
    }
}
