---
paths:
  - 'docs/**'
---

# Docs

## Documentation file stamps

Every markdown file under docs/ starts with this two-line stamp (after YAML front matter if present):

- Updated At: Y-m-d H:i:s
- Reviewed By: model-slug-plus-reasoning-level (e.g. openai/gpt-6-astra-medium) or a GitHub username (e.g. @username)

Refresh Updated At on every edit. Do not stamp GitHub templates that are copied into PR or issue bodies (docs/pull_request_template.md).
