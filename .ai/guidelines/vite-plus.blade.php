{{-- Adapted from the Vite+ AGENTS.md block. Kept verbatim so Pint's laravel_blade rule (pre-commit) doesn't reflow the Markdown. --}}
@verbatim
# Vite+

The frontend toolchain is Vite+, a global CLI called `vp` that wraps Vite, Rolldown, Vitest, Oxlint and Oxfmt. Run `vp help` for its commands and `vp <command> --help` for one command. Docs are local at `node_modules/vite-plus/docs` or online at https://viteplus.dev/guide/.

## Built-in commands vs scripts

`vp <name>` runs a built-in command. `vp run <name>` runs a `package.json` script or a `vite.config.ts` task. Scripts cannot override built-ins, so `vp check` and `vp run check` differ here: the `check` script also runs ESLint on Vue templates. Check `package.json` and `vite.config.ts` first, and use `vp run <name>` when a script or task with that name exists.

## Formatting

- Format JS, TS, Vue and CSS with `vp fmt` (Oxfmt, configured in `vite.config.ts` under `fmt`). Do not run Prettier on these files.
- Prettier is only used by Pint's `laravel_blade` rule to format Blade templates. Format those with `vendor/bin/pint`, not by calling Prettier directly.

## Tool versions

Run `vp toolchain` to show versions in the active Vite+ release, and `vp toolchain <tool>` for one tool. Run `vp why <package>` to show the dependency graph.

## Review checklist

- Run `vp install` after pulling remote changes. It uses the pnpm version pinned in `package.json`.
- Run `vp run check` (format, lint, type check, Vue template lint), `vp run types:check` (vue-tsc for `.vue` files) and `vp test`.
- If setup, runtime or package-manager behavior looks wrong, run `vp env doctor` and include its output when asking for help.
@endverbatim
