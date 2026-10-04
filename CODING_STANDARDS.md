# Coding Standards

Read at review time. Anything Pint, Rector, PHPStan, or Sloppy enforces in CI is out of scope here.

## Sources

- `.ai/rules/` path-scoped project rules; `.ai/rules/index.md` maps globs to files.
- `docs/agents/sloppy-rules.md` describes each Sloppy rule (SL101–SL303) and the fix it expects.

## Fluent method chains

Long or multi-step fluent chains put each `->method()` on its own line, with the receiver on the first line. Short chains that read as one expression (`$request->session()->get('status')`, `Limit::perMinute(5)->by($key)`) stay on one line.
