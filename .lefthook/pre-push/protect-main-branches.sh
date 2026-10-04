#!/usr/bin/env bash

# ! Update this according to your main branches
protected_branches=("main" "staging")

# Check current branch first (most common case)
current_branch=$(git rev-parse --abbrev-ref HEAD 2>/dev/null)

if [ -n "$current_branch" ]; then
  for protected in "${protected_branches[@]}"; do
    if [ "$current_branch" = "$protected" ]; then
      echo "❌ Error: Pushing directly to '$protected' branch is not allowed."
      echo "Please create a pull request instead."
      exit 1
    fi
  done
fi

# Git supplies finite, newline-delimited ref updates on stdin. Skip reads from an
# interactive terminal so manually running this hook does not wait for input.
if [ ! -t 0 ]; then
  while IFS=' ' read -r local_ref local_sha remote_ref remote_sha; do
    if [ -z "$remote_ref" ]; then
      continue
    fi
    
    branch_name=$(echo "$remote_ref" | sed 's|refs/heads/||')
    
    for protected in "${protected_branches[@]}"; do
      if [ "$branch_name" = "$protected" ]; then
        echo "❌ Error: Pushing directly to '$protected' branch is not allowed."
        echo "Please create a pull request instead."
        exit 1
      fi
    done
  done
fi
