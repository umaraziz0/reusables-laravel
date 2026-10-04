{{-- Hand-written. Do not regenerate with `sloppy rules --format=boost`, and strip the CLAUDE.md block `sloppy agents install` merges; the full rule list lives in docs/agents/sloppy-rules.md (`vendor/bin/sloppy rules --format=markdown --output=docs/agents/sloppy-rules.md --force`). --}}
@verbatim
# Sloppy

Before finishing, run `vendor/bin/sloppy diff main --fail-on=high` and fix the findings your change introduced; inherited ones are out of scope. In Claude Code, Sloppy's own hooks (`sloppy agents install`) check each edit and the finish for you.

Fix a failing test by fixing the code, or by correcting what the test asserts and saying why. The diff check reports weakened tests (skipped, deleted, or emptied assertions) as SL503.

Rule descriptions (SL101–SL303): `docs/agents/sloppy-rules.md`.
@endverbatim
