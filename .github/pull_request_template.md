## Summary

<!--
Pick the smallest view that makes the key point clear. Put each visual next to the short text it supports.
Keep only the calls, files, props, states, and boundaries the reviewer needs. Skip preambles.

Views, only when they help:

- Pseudocode for logic or an algorithm
- Call tree for runtime control flow
- Component tree, including important hooks, state, and module boundaries
- Shallow file tree for file responsibility or a broad refactor
- Mermaid for component interaction, control flow, or data flow
- `diff` block when the point is what changes and the surrounding shape already exists
- Full block when most of it is new or the target shape should be copyable

Example of a focused change:

```diff
 submitForm
   createSession
     persistPrompt
+    expandSkillMention
     launchAgent
```
-->

## Evidence

<!--
Concrete proof the change works, as a before and after.
Screenshots are best for visual changes. Otherwise show test results or console output:
the exact test that now fails and passes, as pseudocode.
-->

- **Before:**
  **After:**

## Merge Danger

**Door:** <!-- one-way or two-way. One-way = destructive or hard to reverse. -->

<!-- Optional: description of why. -->

**Blast Radius:** <!-- one word -->

<!-- Optional: potential ramifications of merging (consumer breakage, layout shift, mobile responsiveness, etc.). -->
