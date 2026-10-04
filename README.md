# Laravel & Vue Reusables

![CI](https://github.com/umaraziz0/reusables-laravel/actions/workflows/ci.yml/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

An opinionated starter kit & collection of reusable components etc for Laravel / Vue projects.

## Table of Contents

- [Project Specifications](#project-specifications)
- [Installation](#installation)
- [Development](#development)
- [Component Library](#component-library)
- [Architecture \& Data Transfer Pattern](#architecture--data-transfer-pattern)
- [Contributing](#contributing)
- [CI/CD Workflow](#cicd-workflow)
- [Security \& CORS](#security--cors)
- [Version Release \& Changelog](#version-release--changelog)
- [License](#license)

<br />

## Project Specifications

### Frontend

#### Main

- [Node.js](https://nodejs.org/) v24 or later
- [pnpm](https://pnpm.io/) v12 - package manager
- [TypeScript](https://www.typescriptlang.org/) v6
- [Vue](https://vuejs.org/) v3
- [Inertia.js](https://inertiajs.com/) v3
- [Tailwind CSS](https://tailwindcss.com/) v4
- [Vite](https://vite.dev/) v8 - module bundler

#### Styles

- [shadcn/vue](https://www.shadcn-vue.com/) - base UI component library (built on [Reka UI](https://reka-ui.com/), [class-variance-authority](https://cva.style/) and [tw-animate-css](https://github.com/Wombosvideo/tw-animate-css))
- [Lucide](https://lucide.dev/) - icons
- [vue-sonner](https://github.com/xiaoluoboding/vue-sonner) - toast notifications
- [vue-input-otp](https://github.com/wobsoriano/vue-input-otp) - OTP input (2FA codes)

#### Utilities

- [VueUse](https://vueuse.org/) - various utilities & composables
- [Day.js](https://day.js.org/) - date utility & formatting library

#### Linting & Formatting

- [Vite+](https://viteplus.dev/guide) - unified frontend formatter and linter with anti-slop and security checks
- [ESLint](https://eslint.org/) - Vue template linting with [security](https://github.com/eslint-community/eslint-plugin-security), [no-secrets](https://github.com/nickdeis/eslint-plugin-no-secrets) and [no-unsanitized](https://github.com/mozilla/eslint-plugin-no-unsanitized) plugins

#### Data Fetching

- [ky](https://github.com/sindresorhus/ky) - promise-based HTTP client
- [TanStack Query](https://tanstack.com/query/latest) - data fetching library, with devtools

#### Components

- [vue-turnstile](https://github.com/ruigomeseu/vue-turnstile) - Vue wrapper for Cloudflare Turnstile captcha

#### Others

- [Lefthook](https://github.com/evilmartians/lefthook) - git hooks & staged file processing (format, lint, commitlint)
- [commitlint](https://commitlint.js.org/) - enforces Conventional Commits on `commit-msg`
- [Betterleaks](https://github.com/betterleaks/betterleaks) - secret scanning on pre-commit (`betterleaks git --pre-commit --staged`, via Lefthook) and in CI
- [Release Please](https://github.com/googleapis/release-please) - automated semver releases & changelog via GitHub Actions

### Backend

- [PHP](https://www.php.net/) 8.5
- [Laravel](https://laravel.com/) v13
- [PostgreSQL](https://www.postgresql.org/) v18
- [Redis](https://redis.io/) - cache (`CACHE_STORE=redis`), queues, Horizon

#### Core

- [Laravel Fortify](https://laravel.com/docs/fortify) - headless authentication (login, registration, password reset, email verification, 2FA, passkeys via [`@laravel/passkeys`](https://www.npmjs.com/package/@laravel/passkeys))
- [Laravel Essentials](https://github.com/nunomaduro/essentials) - better Laravel default configs
- [Laravel Permission](https://spatie.be/docs/laravel-permission/v8/introduction) - simple roles & permissions package
- [Laravel Wayfinder](https://github.com/laravel/wayfinder) - BE to FE routing (replaces Ziggy in older Laravel versions)
- [Laravel Sanctum](https://laravel.com/docs/13.x/sanctum#installation) - API authentication
- [Laravolt Indonesia](https://github.com/laravolt/indonesia) - Indonesian regions (provinces, cities, districts, villages)
- [Flysystem S3](https://laravel.com/docs/13.x/filesystem#s3-driver-configuration) - S3-compatible file storage driver

#### Debugging & Monitoring

- [Laravel Telescope](https://laravel.com/docs/13.x/telescope) - view insights into requests, exceptions, queries, etc
- [Laravel Web Tinker](https://github.com/spatie/laravel-web-tinker) - access Laravel Tinker via a web endpoint
- [Log Viewer](https://github.com/opcodesio/log-viewer) - web UI for Laravel logs
- [Laravel Activity Log](https://spatie.be/docs/laravel-activitylog/v5/introduction) - user activity / action logging
- [Laravel Activity Log UI](https://github.com/muhammadsadeeq/laravel-activitylog-ui) - user friendly interface for Activity Log
- [Laravel Self Diagnosis](https://github.com/beyondcode/laravel-self-diagnosis) - environment checks
- [Laravel Horizon](https://laravel.com/docs/13.x/horizon) - redis queue jobs monitoring
- [Laravel Schedule Monitor](https://github.com/spatie/laravel-schedule-monitor) - monitors and logs schedule tasks
- [Laravel Health](https://spatie.be/docs/laravel-health/v1/introduction) - monitors overall application health
- [Laravel Pulse](https://pulse.laravel.com/) - lightweight, self-hosted application monitoring
- [Laravel Nightwatch](https://github.com/laravel/nightwatch) - complete application monitoring & observability platform
- [NewDebugBar](https://github.com/newdebugbar/newdebugbar) - in-browser debug bar (dev only)
- [Laravel Error Solutions](https://github.com/spatie/laravel-error-solutions) - suggested fixes on the error page (dev only)

#### Documentation

- [Laravel Truss](https://github.com/albertoarena/laravel-truss) - read-only database structure export, review, and migration diff tooling
- [Scramble](https://scramble.dedoc.co/) - Laravel OpenAPI documentation generator
- [Laravel Brain](https://github.com/laramint/laravel-brain) - Codebase visualizer (complexity hotspots, security surface, DB access patterns). Access at `/_laravel-brain` after running `php artisan brain:scan`
- Agent / domain docs: [`docs/agents/`](docs/agents/) (issue tracker, triage labels, domain layout) plus root [`AGENTS.md`](AGENTS.md)

Failed queue jobs send `App\Mail\FailedJobNotification` (template: `resources/views/mail/failed-job-notification.blade.php`). Preview as `superadmin` at `/emails/failed-job`.

#### Linting & Formatting

- [Laravel Pint](https://laravel.com/docs/pint) - formatter
- [Rector for Laravel](https://github.com/driftingly/rector-laravel) - automated refactoring & upgrades
- [Larastan](https://github.com/larastan/larastan) - static analysis
- [Sloppy](https://github.com/heyosseus/sloppy) - flags AI-slop code patterns in changed PHP (runs in CI)

#### Security

- [Laravel Scalpel](https://github.com/hryagstn/laravel-scalpel) - filesystem intrusion evidence scanner (runs in CI)

#### Others

- [Pest](https://pestphp.com/) - automated testing, code coverage, type coverage, and test impact analysis
- [Laravel Pail](https://laravel.com/docs/pail) - live log tailing in the terminal
- [Laravel Octane](https://laravel.com/docs/octane) - serve application using high-powered servers ([FrankenPHP](https://frankenphp.dev/) via `OCTANE_SERVER`)
- [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper) - IDE autocompletion stubs (regenerated on `composer update`)

#### AI Agent Tooling

- [Laravel Boost](https://github.com/laravel/boost) - MCP server
- [Spatie Boost Guidelines](https://github.com/spatie/boost-spatie-guidelines) - Spatie's Laravel & PHP coding guidelines for Boost
- [Laravel Auditor](https://github.com/mrpunyapal/laravel-auditor) - evidence-based codebase audit tooling for agents
- [Pest Agent Plugin](https://github.com/pestphp/pest-plugin-agent) - one-shot verification CLI for agents
- [Laravel PAO](https://github.com/laravel/pao) - agent-optimized output for PHP testing tools

<br />

## Installation

### Express Setup

Clone the repository and run:

```bash
composer run setup
```

This script automatically:

- Installs PHP dependencies via Composer
- Copies `.env.example` to `.env` (if missing)
- Generates application key
- Runs app and testing database migrations
- Creates the `storage` symlink
- Installs Node dependencies via pnpm
- Installs the Lefthook Git hooks

After setup, ensure **[Betterleaks](https://github.com/betterleaks/betterleaks)** is installed on your machine so the pre-commit secret scan can run (for example: `brew install betterleaks` on macOS).

### Manual Setup

If you prefer manual installation:

1. **Clone and Install Dependencies**

   ```bash
   git clone https://github.com/umaraziz0/reusables-laravel.git
   cd reusables-laravel
   composer install
   pnpm install
   pnpm exec lefthook install
   ```

2. **Environment Setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database & Storage**

   ```bash
   php artisan migrate
   php artisan migrate --env=testing
   php artisan storage:link
   ```

<br />

### Installation Checklist

1. Install & setup a PostgreSQL database
2. Install & run Redis (required for cache tags, queues, and Horizon; `.env.example` sets `CACHE_STORE=redis`)
3. Install the project using the above Express / Manual Setup
4. Install [Betterleaks](https://github.com/betterleaks/betterleaks) for secret scanning

For new projects using this as base:

5. Create blank repository in Github
6. Remove old github repository origin (reusables-laravel)
7. Add new github repository origin and push to this new repository
8. Create `staging` and `dev` branches locally
9. Choose the release branch (`main` by default). If changing it to `dev`, update both `.github/workflows/release.yml` and the release instructions in this README, then checkout `dev` to make changes
10. Set `version` to `0.1.0` on `package.json`, `composer.json`, and `.release-please-manifest.json`
11. Set `package-name` to the new project's name on `release-please-config.json`
12. Change Github default branch from `main` to `dev`
13. Commit changes and push both the `dev` and `staging` branches
14. Enable dependabot on Github repository

Before going live, work through [`docs/pre-production-checklist.md`](docs/pre-production-checklist.md).

## Development

Start the development server:

```bash
composer run dev
```

This runs `php artisan dev`, which starts concurrently (list them with `php artisan dev:list`):

- Octane server with file watching (`octane:start --watch`)
- Horizon queue workers
- Application logs (Pail)
- Vite development server

With `composer run dev`, the app is at http://localhost:8000. With Laravel Herd (default `APP_URL`), use http://reusables-laravel.test.

To build the client and SSR bundles:

```bash
pnpm run build
```

<br />

## Component Library

Superadmins can browse the available reusable UI component previews at **`/components`**.

Visiting `/components` redirects to the Buttons preview. Use the sidebar on any preview page to switch between sections.

<br />

## Architecture & Data Transfer Pattern

API flow: **Form Request → Controller → Action → Model** (Observers for cache side effects, Resources for response shape).

Business logic, queries, and caching live in single-purpose Action classes under `app/Actions/` (`Verb+Noun`, one `handle()`).

| Layer         | Path                        | Role                                                                            |
| ------------- | --------------------------- | ------------------------------------------------------------------------------- |
| Models        | `app/Models/`               | Typed attrs, `CACHE_*` constants, `#[Fillable]` / `#[Hidden]` / `#[ObservedBy]` |
| Form Requests | `app/Http/Requests/`        | Validation                                                                      |
| Actions       | `app/Actions/`              | Business logic, data access, caching                                            |
| Observers     | `app/Observers/`            | Granular cache invalidation and other model-related events                      |
| API Resources | `app/Http/Resources/`       | API response shaping                                                            |
| Controllers   | `app/Http/Controllers/API/` | Thin orchestration + `ApiResponseHelpers`                                       |
| Routes        | `routes/api.php`            | REST + Sanctum + ability middleware                                             |
| Logging       | `app/Actions/Logging/`      | `WriteLog`, `LogException`, `LogActivity`, `RedactSensitiveData`, etc.          |

**Working examples:** `app/Http/Controllers/API/UserController.php`, `app/Models/User.php`, `app/Actions/User/`, `app/Observers/UserObserver.php`.

**Practices:** Form Requests for validation; put business logic / queries / caching in Actions (not controllers, repositories, or services); cache in data-access Actions with `select(CACHE_ATTRIBUTES)`; invalidate via observers (`CACHE_TAG_ITEM` / `CACHE_TAG_LIST`); transform with Resources; respond via `ApiResponseHelpers`; log via logging Actions (`WriteLog`, `LogException`, `LogActivity`, `RedactSensitiveData`) — not a `LogService`.

<br />

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for commit message conventions, pre-commit hooks, and branching strategy.

<br />

## CI/CD Workflow

This project uses GitHub Actions for continuous integration and deployment. The CI workflow runs automatically on every pull request to ensure code quality and test coverage.

### Workflow Overview

The CI workflow (`.github/workflows/ci.yml`) consists of three jobs:

#### Secrets Job

- **Betterleaks**: Scans incoming pull-request commits for leaked secrets (staged-diff scan, redacted output)

#### Lint Job

The lint job validates code quality and formatting:

**Backend Checks:**

- **Laravel Pint**: Validates PHP code formatting
- **Rector**: Checks for code improvements and modernization opportunities
- **PHPStan**: Performs static analysis to catch potential bugs
- **Sloppy**: Reviews changed PHP for AI-slop patterns (`php artisan sloppy:ci`)
- **Scalpel**: Scans for intrusion evidence (structural, obfuscated, `.htaccess`, `.user.ini`, env files) and fails on HIGH findings

**Frontend Checks:**

- **Vite+**: Runs unified frontend formatting, lint, anti-slop, and security checks (`pnpm run check`)
- **Wayfinder**: Regenerates frontend routes and actions (`php artisan wayfinder:generate --with-form`)
- **vue-tsc**: Type-checks the frontend (`pnpm run types:check`)

#### Tests Job

The tests job runs the full test suite:

- **Truss**: Migrates a fresh PostgreSQL database and checks the schema (`truss:doctor --fail-on=warning`)
- **Type Coverage**: Ensures 100% type coverage across the codebase
- **Code Coverage**: Runs all Pest tests & maintains minimum 80% code coverage requirement

### TIA Baseline

`.github/workflows/tia-baseline.yml` runs on every push to `main` (or manually). It runs the suite with Pest test impact analysis (`pest --parallel --tia --fresh`) and uploads the baseline as an artifact.

### Running Locally

You can run the same checks locally before pushing:

```bash
composer test
```

<br />

## Security & CORS

### Login & CAPTCHA (Turnstile)

Cloudflare Turnstile is **scaffolded** but not wired into Fortify login yet:

- Env: `VITE_TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET_KEY` (see `.env.example` / `config/services.php`)
- FE: [vue-turnstile](https://github.com/ruigomeseu/vue-turnstile) installed; CSP allow-lists Turnstile
- BE: [`ValidateCaptcha`](app/Actions/Auth/ValidateCaptcha.php) + [`ValidCaptcha`](app/Rules/ValidCaptcha.php)

### Security Headers

See `app/Http/Middleware/SecurityHeaders.php` (CSP, frame options, HSTS in production, Permissions-Policy).

- Dev CSP allows Vite HMR (`localhost:5173`) and `unsafe-eval`.
- Prod uses nonce-based **scripts** (no script `unsafe-inline` / `unsafe-eval`); `style-src` keeps `unsafe-inline` in both envs for Vue `:style` bindings.
- External allow-list: Turnstile, Google/Bunny fonts.

CSP is skipped (other headers still apply) for `api/*` and every admin tool in [`App\Enums\AdminTool`](app/Enums/AdminTool.php): API docs, email previews, health, Horizon, Telescope, Pulse, Activity Log UI, Log Viewer, Web Tinker, and Laravel Brain. Tool paths come from each package's config where one exists. Add a case to the enum to register a new admin tool; the enum also defines its access gate (local env or `superadmin`).

Blade inline scripts: `<script nonce="{{ $cspNonce }}">`.

### CORS

Configured in `config/cors.php`. Paths default to `api/*` and `sanctum/csrf-cookie`. Origins from `APP_URL` plus `CORS_ALLOWED_ORIGINS`:

```env
CORS_ALLOWED_ORIGINS=https://app.example.com,https://admin.example.com
CORS_MAX_AGE=86400
CORS_SUPPORTS_CREDENTIALS=false
```

<br />

## Version Release & Changelog

[Release Please](https://github.com/googleapis/release-please) runs via [`.github/workflows/release.yml`](.github/workflows/release.yml). Version bumps follow [Conventional Commits](CONTRIBUTING.md#commit-message-conventions) (`feat` → minor, `fix`/`perf`/… → patch, `BREAKING CHANGE` / `!` → major).

1. Merge conventional commits to the release branch (`main` by default).
2. Release Please opens a **Release** PR (bumps `package.json` / `composer.json`, updates `CHANGELOG.md`).
3. Merge that PR to tag and publish the GitHub release.

No manual tagging needed.

<br />

## License

Released under the [MIT License](LICENSE).
