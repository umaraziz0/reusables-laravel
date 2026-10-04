#!/usr/bin/env bash
set -euo pipefail

# Reject commits that include paths matching known secret / credential filenames.
# This runs on the Git index (staged files), not on .gitignore, so it still blocks
# accidental `git add -f` or a missing ignore rule.

violations=()

contains_private_key_header() {
  if ! git show ":./$1" 2>/dev/null >"$inspection_file"; then
    return 2
  fi

  LC_ALL=C grep -a -qi -- '-----BEGIN .*PRIVATE KEY-----' "$inspection_file"
}

staged_paths_file=$(mktemp "${TMPDIR:-/tmp}/block-sensitive-paths.XXXXXX")
inspection_file=$(mktemp "${TMPDIR:-/tmp}/block-sensitive-paths-inspection.XXXXXX")
trap 'rm -f "$staged_paths_file" "$inspection_file"' EXIT

if ! git diff --cached --name-only --diff-filter=ACMR -z >"$staged_paths_file"; then
  echo "❌ Unable to inspect staged paths; refusing commit."
  exit 1
fi

while IFS= read -r -d '' path; do
  [[ -z "$path" ]] && continue

  base=${path##*/}
  extension=
  if [[ "$base" == *.* ]]; then
    extension=${base##*.}
    extension=$(printf '%s' "$extension" | tr '[:upper:]' '[:lower:]')
  fi
  blocked=
  reason=

  if [[ "$base" == ".env.example" ]] || [[ "$base" == ".env.testing" ]]; then
    continue
  fi

  if [[ "$base" == ".env" ]] || [[ "$base" == .env.* ]]; then
    blocked=1
    reason="environment file (.env or .env.* — only .env.example and .env.testing belong in Git)"
  fi

  if [[ "$base" == "auth.json" ]]; then
    blocked=1
    reason="Composer auth.json"
  fi

  if [[ "$path" == storage/* && "$extension" == "key" ]]; then
    blocked=1
    reason="Laravel storage key file (*.key)"
  elif [[ "$extension" == "key" ]]; then
    if contains_private_key_header "$path"; then
      blocked=1
      reason="private key file (*.key)"
    elif (( $? > 1 )); then
      blocked=1
      reason="unable to inspect potential private key file (*.key)"
    fi
  fi

  if [[ "$base" == "Homestead.yaml" ]] || [[ "$base" == "Homestead.json" ]]; then
    blocked=1
    reason="Homestead local config (often contains secrets)"
  fi

  if [[ "$extension" =~ ^(p12|pfx|jks)$ ]]; then
    blocked=1
    reason="certificate / keystore"
  elif [[ "$extension" == "pem" ]]; then
    if contains_private_key_header "$path"; then
      blocked=1
      reason="private PEM key"
    elif (( $? > 1 )); then
      blocked=1
      reason="unable to inspect potential private PEM key"
    fi
  fi

  if [[ "$base" =~ ^id_(rsa|ed25519|ecdsa)$ ]]; then
    blocked=1
    reason="SSH private key"
  fi

  if [[ -n "${blocked:-}" ]]; then
    violations+=("$path — $reason")
  fi
done <"$staged_paths_file"

if ((${#violations[@]} > 0)); then
  echo "❌ Refusing commit: staged paths look like local secrets or credentials."
  echo ""
  for v in "${violations[@]}"; do
    echo "  - $v"
  done
  echo ""
  echo "Remove these from the index (e.g. git reset HEAD -- <file>), add them to .gitignore, and do not force-add local env files (.env.example and .env.testing are allowed)."
  exit 1
fi
