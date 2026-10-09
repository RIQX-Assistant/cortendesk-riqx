<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * RIQX: GET /locale/{locale} — the language switch on the topbar and the
 * sign-in pages. Saved on the account when signed in, and in a cookie so the
 * sign-in page follows too.
 */
class LocaleController extends Controller
{
    public function update(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('riqx.locales', [])), 404);

        $request->user()?->forceFill(['locale' => $locale])->save();

        // Only ever bounce back to a page of this console.
        $back = url()->previous();
        if (! str_starts_with($back, url('/')) || str_contains($back, '/locale/')) {
            $back = $request->user() ? route('overview') : route('login');
        }

        return redirect()->to($back)
            ->withCookie(cookie()->forever(SetLocale::COOKIE, $locale));
    }
}
