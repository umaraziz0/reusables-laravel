---
paths:
  - 'database/migrations/**'
---

# Migrations

## Typed Config in migrations

Prefer Config::string/integer/boolean/array for known types. Use Config::get + is_string for optional DB connections that may be null. Do not use config('key') helper reads (enforced by ArchTest filesystem scan).
