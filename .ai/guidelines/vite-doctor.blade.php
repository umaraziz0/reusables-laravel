{{-- Hand-written. Kept verbatim so Pint's laravel_blade rule (pre-commit) doesn't reflow the Markdown. --}}
@verbatim
# Vite Doctor

Vite Doctor checks the Vue code for framework bugs that Oxlint misses. `pnpm run check` runs it as `pnpm run check:doctor`, and the lefthook pre-commit hook runs it on changed lines.

Before finishing a change to `resources/js`, run `pnpm exec vite-doctor . --changed --format agent` and fix the findings your change introduced. Use the `doctor` skill for the workflow.

Suppress a finding only when it is a false positive, on the line above, with a reason: `<!-- doctor-disable-next-line <rule> -- <reason> -->` in a template, `// doctor-disable-next-line <rule> -- <reason>` in script code. Excluded paths live in `doctor.config.json`.

Never run `pnpm doctor`: it runs pnpm's own health check, not Vite Doctor.
@endverbatim
