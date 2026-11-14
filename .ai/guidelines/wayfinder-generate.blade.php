{{-- Hand-written. Kept verbatim so Pint's laravel_blade rule (pre-commit) doesn't reflow the Markdown. --}}
@verbatim
# Wayfinder generation

The Wayfinder Vite plugin regenerates `resources/js/actions` and `resources/js/routes` with form variants (`formVariants: true` in `vite.config.ts`) on `vp run build`, `vp dev` and `vp test`. Prefer those over running the generator by hand.

If you run it by hand, pass `--with-form`: `php artisan wayfinder:generate --with-form`. Without it, the `.form()` helpers that `<Form>` pages use disappear and `vp run types:check` fails.
@endverbatim
