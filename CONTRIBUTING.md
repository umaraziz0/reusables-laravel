# Contributing

## Table of Contents

- [Commits \& Branching](#commits--branching)
  - [Commit Message Conventions](#commit-message-conventions)
  - [Pre-commit hooks (Lefthook)](#pre-commit-hooks-lefthook)
  - [Branching Strategy](#branching-strategy)
    - [Primary Branches](#primary-branches)
    - [Branch Naming Convention](#branch-naming-convention)
    - [Branching Workflow](#branching-workflow)
    - [Stacked / dependent branches](#stacked--dependent-branches)
    - [Best Practices](#best-practices)

## Commits & Branching

### Commit Message Conventions

This project follows [Conventional Commits](https://www.conventionalcommits.org/) specification. Commit messages should follow this format:

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**

Based on [Angular conventions](https://github.com/angular/angular/blob/22b96b96902e1a42ee8c5e807720424abad3082a/CONTRIBUTING.md#type), must be one of the following:

- **build**: Changes that affect the build system or external dependencies (example scopes: gulp, broccoli, npm)
- **ci**: Changes to our CI configuration files and scripts (example scopes: Travis, Circle, BrowserStack, SauceLabs)
- **docs**: Documentation only changes
- **feat**: A new feature
- **fix**: A bug fix
- **perf**: A code change that improves performance
- **refactor**: A code change that neither fixes a bug nor adds a feature
- **style**: Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc)
- **test**: Adding missing tests or correcting existing tests

**Examples:**

```bash
feat(auth): add two-factor authentication
fix(security): resolve XSS vulnerability in user input
perf(api): optimize database queries
BREAKING CHANGE: refactor(user): change user model structure
```

### Pre-commit hooks (Lefthook)

Git hooks are defined in [`lefthook.yml`](lefthook.yml). On each commit, Lefthook typically runs:

- **Frontend**: Vite+ staged checks (`vp staged`) for JS/TS/Vue (and related) files, including anti-slop and security rules
- **Backend**: Pint, Rector, and PHPStan for staged PHP and Blade files
- **Security**: [Betterleaks](https://github.com/betterleaks/betterleaks) `git --pre-commit --staged` (with `--redact`) to block secrets in staged changes; CI scans incoming pull-request commits (see `.github/workflows/ci.yml`)
- **Commit message**: [Conventional Commits](https://www.conventionalcommits.org/) lint via Commitlint

Install Betterleaks globally or via your package manager so `betterleaks` is on your `PATH`.

Pre-commit runs a **staged-diff scan**, not a full `git log` walk—verbose output should show bytes scanned on the staged changes.

### Branching Strategy

This project uses a three-tier branching strategy with primary branches and feature/fix branches.

#### Primary Branches

- **`main`**: Production-ready code. Only receives merges from `staging` after thorough testing. Maintainers only.
- **`staging`**: Pre-production environment. Receives merges from `dev` for final testing before production. Maintainers only.
- **`dev`**: Development integration branch. All feature and fix work lands here via pull request (minimum 1 approving review).

#### Branch Naming Convention

Feature and fix branches follow a hierarchical naming pattern:

```
<type>/<scope>/<description>
```

**Format:**

- **Type**: `feat` for features, `fix` for bug fixes
- **Scope**: Category or module (e.g., `auth`, `security`, `api`, `ui`)
- **Description**: Kebab-case description of the change

**Examples:**

```bash
feat/auth/two-factor-authentication
feat/api/user-profile-endpoint
fix/security/xss-vulnerability
fix/database/query-optimization
```

#### Branching Workflow

1. **Create a feature/fix branch** from `dev`:

   ```bash
   git checkout dev
   git pull origin dev
   git checkout -b feat/auth/two-factor-authentication
   ```

2. **Work on your branch** and commit changes following conventional commit format.

3. **Open a pull request** into `dev` when ready:

   ```bash
   git push -u origin feat/auth/two-factor-authentication
   gh pr create --base dev
   ```

4. **Merge only after review**: at least **1 approving review** is required before the PR can be merged into `dev`. Do not merge your own PR without that approval.

Promotion of `dev` → `staging` → `main` is handled by maintainers, not individual contributors.

#### Stacked / dependent branches

When task B depends on task A, and A’s PR is not yet reviewed or merged into `dev`:

1. **Branch B from A**, not from `dev`:

   ```bash
   git checkout feat/auth/two-factor-authentication
   git checkout -b feat/auth/recovery-codes
   ```

2. **Open PR B with base = branch A** so the diff shows only B’s commits:

   ```bash
   git push -u origin feat/auth/recovery-codes
   gh pr create --base feat/auth/two-factor-authentication
   ```

3. **Note the dependency** in PR B’s description (e.g. `Depends on #123` or “blocked by PR for A”).

4. **After A merges into `dev`**, retarget and rebase B:

   ```bash
   gh pr edit --base dev
   git fetch origin
   git rebase origin/dev
   git push --force-with-lease
   ```

   Then review and merge B normally into `dev`.

**Guidelines:**

- Stack only when B **truly** needs A’s code. Independent work still branches from `dev`.
- Keep stacks short (2–3 deep). Deeper stacks get painful to rebase.
- Review order: merge **A first**, then B. Do not merge B into `dev` while A is still open.
- If A gets review changes, rebase B onto the updated A branch.
- Draft PRs for B are fine until A lands.

#### Best Practices

- Branch from `dev` for new features or fixes, unless the work depends on an open PR — then branch from that PR’s branch (see [Stacked / dependent branches](#stacked--dependent-branches))
- Keep branches focused on a single feature or fix
- Use descriptive branch names that match your commit message scope
- Always open a pull request — never merge feature/fix branches locally into `dev`
- Wait for at least 1 PR approval before merging
- Delete branches after merging to keep the repository clean
- Never commit to or merge into `main` or `staging` branches
- Ensure all tests and CI checks pass before merging to `dev`
