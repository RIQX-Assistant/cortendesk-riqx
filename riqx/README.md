# RIQX fork of CortenDesk

This is a modified version of [CortenDesk](https://github.com/marcpope/cortendesk)
(AGPL-3.0), maintained by RIQX. Changes on top of upstream (branch `riqx`):

- **Languages:** every console string goes through Laravel's translator.
  `lang/fa.json` (Persian) and `lang/en.json` (brand wording), `lang/fa/*.php`
  (validation). A per-user language (`users.locale`) with a switch in the top
  bar, on the sign-in pages and in My Account (`App\Http\Middleware\SetLocale`).
- **Right-to-left:** Persian pages load Bootstrap RTL and the `*.rtl.css`
  copies made by `riqx/build-rtl.sh` (rtlcss), with the Vazirmatn font.
- **Dates:** `App\Support\LocalDate` prints Solar Hijri dates in Persian and
  shows every date in `RIQX_DISPLAY_TIMEZONE` (default Asia/Tehran).
- **Brand:** name, colours (`public/assets/css/riqx.css`) and logo files in
  `public/riqx-brand` (mount a folder over it to change the logo without a
  rebuild). Upstream update notices are off by default.
- **Image:** `.github/workflows/riqx-image.yml` -> `ghcr.io/riqx-assistant/cortendesk-riqx`.

Settings: `config/riqx.php`. Merging upstream: merge the tag, run
`riqx/build-rtl.sh`, translate new strings in `lang/fa.json`, bump
`riqx.revision`.
