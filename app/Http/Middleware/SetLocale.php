<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * RIQX: picks the console language — the signed-in user's saved choice, else
 * the language switch cookie, else config('riqx.default_locale').
 */
class SetLocale
{
    public const COOKIE = 'riqx_locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = self::resolve($request);

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }

    public static function resolve(Request $request): string
    {
        $supported = array_keys(config('riqx.locales', []));

        $saved = $request->user()?->locale;
        if (in_array($saved, $supported, true)) {
            return $saved;
        }

        $cookie = $request->cookie(self::COOKIE);
        if (in_array($cookie, $supported, true)) {
            return $cookie;
        }

        $default = config('riqx.default_locale');

        return in_array($default, $supported, true) ? $default : 'en';
    }
}
