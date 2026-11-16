#!/usr/bin/env bash
set -euo pipefail

# Create or update the triage labels (docs/agents/triage-labels.md) on a GitHub repo.
# Safe to re-run: `gh label create --force` updates labels that already exist.
#
# Usage: bash .github/scripts/setup-labels.sh [owner/repo]
# Without an argument, gh uses the repo of the current directory's remote.

repo_args=()
if [[ $# -gt 0 ]]; then
  repo_args=(--repo "$1")
fi

labels=(
  "needs-triage|d4c5f9|Maintainer needs to evaluate this issue"
  "needs-info|fbca04|Waiting on reporter for more information"
  "ready-for-agent|0e8a16|Fully specified, ready for an AFK agent"
  "ready-for-human|1d76db|Requires human implementation"
  "wontfix|ffffff|Will not be actioned"
)

for label in "${labels[@]}"; do
  IFS='|' read -r name color description <<<"$label"
  gh label create "$name" --color "$color" --description "$description" --force "${repo_args[@]+"${repo_args[@]}"}"
  echo "✔ $name"
done
