# Upstream provenance

- Source: https://github.com/dmmulroy/anti-slop
- Installed path: `tools/oxlint/anti-slop/`
- Updated: 2026-09-16
- Incoming snapshot: the `install-anti-slop` skill bundle recorded in
  `skills-lock.json` with computed hash
  `4031728fbe75bdcad6ee3208fd52b5d66e167b056fefee1fa9758e9a6cb9c0c8`
- Incoming upstream commit: unknown because the skill lock records a content hash,
  not the source commit
- Recoverable base: repository commit `5046b2d` contains the original installed
  files; later local Vite+ compatibility changes are preserved from `c8b0a2e`

## Local decisions

- Rule code imports `vite-plus/lint/plugins` instead of `@oxlint/plugins` so the
  plugin uses the same compatibility layer as this repository's Vite+ lint runner.
- All generic rules shipped in the incoming bundle are registered and enabled in
  `vite.config.ts`.
- The optional Effect rules remain unregistered because this application does not
  declare Effect as a direct dependency.
- The vendored ESLint Stylistic license and provenance files are retained with the
  readable-spacing rule.

## Verification

- `vue-tsc --noEmit` passes.
- A focused `no-array-filter-map` probe rejects `filter().map()` and accepts the
  equivalent `flatMap()` form.
- `vp check` loads the plugin and passes formatting, but reports 40 existing
  `require-readable-spacing` findings in application source. This update leaves
  those files unchanged.

Future updates should use the incoming content hash above and repository commit
`5046b2d` when reconstructing the three-way comparison.
