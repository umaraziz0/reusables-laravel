<!-- Optional. Ticket, plan, or other relevant links. Delete this line when there are none. -->
[Ticket](url) | [Plan](url)

## Why the change

<!-- Exactly one sentence: the problem this PR solves, and what becomes possible after it ships. -->

## Special things to note

<!-- 1–3 reviewer warnings, migrations, compatibility constraints, deliberate omissions, or surprising decisions. -->

- None.

## Change outline

<!--
Use the smallest set of views that explains the implementation.
Prefer diff blocks for a change to an existing shape.
Show the complete target shape when most of it is new.
Omit any view that does not apply, and order the ones you keep so the story is easy to follow.
One short line above each block is enough.

Views, only when they help:

- SQL tables, important columns and relationships, and endpoint request/response contracts
- Key data structures or types
- Pseudocode for the changed behavior
- A shallow file tree of changed responsibilities
- Component trees, including important hooks, state, and package boundaries
- Call trees, call stacks, control flow, or data flow

Example of a focused change:

```diff
 submitForm
   createSession
     persistPrompt
+    expandSkillMention
     launchAgent
```
-->
