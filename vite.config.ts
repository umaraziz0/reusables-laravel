import cloudflare from '@cloudflare/oxlint-config/vite-plus';
import inertia from '@inertiajs/vite';
import landminesRecommended from '@interfere/landmines/configs/recommended.json' with { type: 'json' };
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig, lazyPlugins, loadEnv } from 'vite-plus';

// Vitest starts Vite in serve mode, which laravel-vite-plugin refuses to do
// when CI is set. Component tests don't serve assets, so skip that check.
if (process.env.VITEST) {
  process.env.LARAVEL_BYPASS_ENV_CHECK = '1';
}

const env = loadEnv('', process.cwd(), '');

/**
 * Mirrors `app.component_previews` in config/app.php: component preview pages
 * are bundled everywhere except production.
 */
const isComponentPreviewsEnabled =
  (env.APP_ENV ?? 'production') !== 'production';

/**
 * Turns off the preset's React and JSX rules. This app has no JSX, and the
 * React compiler rules misread Vue composables as React hooks.
 */
/**
 * Enables landmines' `recommended` rules (its correctness and suspicious
 * categories). The JSON import widens severities to `string`, so the rule
 * names are read from it and set to `error` here.
 */
const landminesRules = Object.fromEntries(
  Object.keys(landminesRecommended.rules).map((rule) => [
    rule,
    'error' as const,
  ]),
);

const disabledReactRules = Object.fromEntries(
  Object.keys(cloudflare.rules ?? {})
    .filter((rule) => /^(react|jsx-a11y)\//.test(rule))
    .map((rule) => [rule, 'off' as const]),
);

export default defineConfig({
  define: {
    __COMPONENT_PREVIEWS__: JSON.stringify(isComponentPreviewsEnabled),
  },
  plugins: lazyPlugins(() => [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.ts'],
      refresh: true,
      fonts: [
        bunny('Instrument Sans', {
          weights: [400, 500, 600],
        }),
      ],
    }),
    inertia(),
    tailwindcss(),
    vue({
      template: {
        transformAssetUrls: {
          base: undefined,
          includeAbsolute: false,
        },
      },
    }),
    wayfinder({
      formVariants: true,
    }),
  ]),
  server: {
    watch: {
      ignored: [
        '**/.ai/**',
        '**/.agents/**',
        '**/.claude/**',
        '**/.cursor/**',
        '**/.junie/**',
        '**/vendor/**',
      ],
    },
    hmr: {
      host: 'localhost', // Use localhost (IPv4) for HMR to avoid IPv6 CSP parsing issues
    },
  },
  lint: {
    extends: [cloudflare],
    env: cloudflare.env,
    ignorePatterns: [
      ...cloudflare.ignorePatterns,
      'vendor/**',
      'node_modules/**',
      'public/**',
      'bootstrap/ssr/**',
      'tailwind.config.js',
      'resources/js/actions/**',
      'resources/js/components/ui/**',
      'resources/js/routes/**',
      'resources/js/wayfinder/**',
      'reports/**',
      '.agents/**',
      '.claude/**',
      '.codex/**',
      '.cursor/**',
      '.pi/**',
      '.ai/**',
    ],
    plugins: ['unicorn', 'typescript', 'oxc', 'vue'],
    jsPlugins: [
      {
        name: 'shadcn',
        specifier: '@shadcn/lint',
      },
      {
        name: 'security',
        specifier: 'eslint-plugin-security',
      },
      {
        name: 'no-unsanitized',
        specifier: 'eslint-plugin-no-unsanitized',
      },
      {
        name: 'no-secrets',
        specifier: 'eslint-plugin-no-secrets',
      },
      {
        name: 'landmines',
        specifier: '@interfere/landmines',
      },
    ],
    categories: {
      correctness: 'error',
    },
    options: {
      denyWarnings: true,
      typeAware: true,
      typeCheck: true,
    },
    rules: {
      'shadcn/no-restyle': ['off', { allow: ['layout'] }],
      'shadcn/no-raw-colors': 'error',
      'shadcn/no-arbitrary-values': 'off',
      'shadcn/no-inline-styles': 'off',
      'shadcn/no-unknown-classes': 'error',
      'shadcn/require-static-classes': 'off',
      'security/detect-buffer-noassert': 'warn',
      'security/detect-child-process': 'warn',
      'security/detect-disable-mustache-escape': 'warn',
      'security/detect-eval-with-expression': 'warn',
      'security/detect-new-buffer': 'warn',
      'security/detect-no-csrf-before-method-override': 'warn',
      'security/detect-non-literal-fs-filename': 'warn',
      'security/detect-non-literal-regexp': 'warn',
      'security/detect-non-literal-require': 'warn',
      'security/detect-object-injection': 'off',
      'security/detect-possible-timing-attacks': 'warn',
      'security/detect-pseudoRandomBytes': 'warn',
      'security/detect-unsafe-regex': 'warn',
      'security/detect-bidi-characters': 'warn',
      'no-unsanitized/property': 'error',
      'no-unsanitized/method': 'error',
      ...landminesRules,
      ...disabledReactRules,
      // Hex values here are code points, which read better ungrouped.
      'unicorn/numeric-separators-style': [
        'error',
        { hexadecimal: { onlyIfContainsSeparator: true } },
      ],
    },
    overrides: [
      {
        files: ['resources/js/**/*.vue'],
        rules: {
          // Vue compiler macros (defineProps, defineModel, ...) are not
          // globals; typeCheck already reports undefined names.
          'no-undef': 'off',
          'shadcn/no-restyle': 'off',
          'shadcn/no-raw-colors': 'off',
          'shadcn/no-arbitrary-values': 'off',
          'shadcn/no-inline-styles': 'off',
          'shadcn/no-unknown-classes': 'off',
          'shadcn/require-static-classes': 'off',
        },
      },
      {
        files: ['resources/js/types/global.d.ts'],
        rules: {
          'quality/no-unsafe-dictionary-type': 'off',
        },
      },
      {
        files: ['resources/js/**/*.{js,ts,vue}'],
        rules: {
          'no-secrets/no-secrets': 'error',
        },
      },
    ],
  },
  fmt: {
    printWidth: 80,
    tabWidth: 2,
    singleQuote: true,
    semi: true,
    singleAttributePerLine: false,
    htmlWhitespaceSensitivity: 'css',
    ignorePatterns: [
      '.github/**',
      'composer.json',
      'package.json',
      'pnpm-lock.yaml',
      'README.md',
      'CHANGELOG.md',
      'docs/**',
      'AGENTS.md',
      'CLAUDE.md',
      'boost.json',
      '.agents/**',
      '.claude/**',
      '.codex/**',
      '.cursor/**',
      '.pi/**',
      '.ai/**',
      'vendor/**',
      'node_modules/**',
      'public/**',
      'bootstrap/ssr/**',
      'tailwind.config.js',
      'resources/js/actions/**',
      'resources/js/components/ui/**',
      'resources/js/routes/**',
      'resources/js/wayfinder/**',
      'resources/views/mail/**',
    ],
    sortTailwindcss: {
      functions: ['clsx', 'cn', 'cva'],
      stylesheet: 'resources/css/app.css',
    },
    sortImports: {
      groups: [
        'type',
        'builtin',
        'external',
        'internal',
        ['parent', 'sibling', 'index'],
        'style',
        'unknown',
      ],
      internalPattern: ['@/'],
      sortSideEffects: false,
    },
  },
  staged: {
    '**/*.{js,ts,vue,css,md,yml,yaml,json}': 'vp check --fix',
  },
  test: {
    environment: 'happy-dom',
    include: ['resources/js/**/*.test.ts'],
  },
});
