---
paths:
  - 'app/**'
---

# App

## Prefer typed Config facade accessors

Read config with Config::string/integer/boolean/array for known types. Use Config::get + is_* when null/non-matching types are legitimate (e.g. optional queue names). Cast env values in config/*.php. Do not use the config() helper in App (banned by ArchTest); use Config::set for writes. Tests may use config([...]) for writes only.

## WriteLog three-part operational messages

Operational logs go through WriteLog. Pass `module`, `action`, and `message` separately (modules API, CMS, Jobs, etc; Title Case action; lowercase message). WriteLog joins them as `{Module} - {Action} - {message}`. Wrappers in `app/Actions/Logging` take the same three params. Do not call Log:: directly.
