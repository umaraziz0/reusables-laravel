---
paths:
  - 'tests/**'
---

# Tests

## Typed Config reads in tests

Prefer Config::string/integer/boolean/array for config reads. Keep config([...]) or Config::set for test overrides. Do not use config('key') string-key reads (enforced by ArchTest filesystem scan).

## Array item layout

Put each PHP array on one line, or each direct item on its own line with both brackets on their own lines; a single nested-array item (`[$attribute => [ ... ]]`) may share the wrapper's lines. PHPStan enforces this as `arrayLayout.itemPerLine`; only `tests/PHPStan/Fixtures` is excluded.
