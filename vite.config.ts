import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig, lazyPlugins } from 'vite-plus';

export default defineConfig({
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
    ignorePatterns: [
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
      'tools/oxlint/anti-slop/**',
      '.agents/**',
      '.claude/**',
      '.codex/**',
      '.cursor/**',
      '.pi/**',
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
        name: 'anti-slop',
        specifier: './tools/oxlint/anti-slop/index.ts',
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
      'oxc/no-accumulating-spread': 'error',
      'anti-slop/no-array-filter-map': 'error',
      'anti-slop/no-reduce-accumulator-copy': 'error',
      'anti-slop/no-chained-type-assertions': 'error',
      'anti-slop/no-conditional-empty-object-spread': 'error',
      'anti-slop/no-known-value-widening': 'error',
      'anti-slop/no-module-mocking': 'error',
      'anti-slop/no-object-parameters': 'error',
      'anti-slop/no-reflect-apply': 'error',
      'anti-slop/no-reflect-get': 'error',
      'anti-slop/no-runtime-typeof': 'error',
      'anti-slop/no-shape-in-symbol-names': 'error',
      'anti-slop/no-unknown-parameters': 'error',
      'anti-slop/no-unknown-returns': 'error',
      'anti-slop/no-unknown-type-aliases': 'error',
      'anti-slop/no-unsafe-dictionary-type': 'error',
      'anti-slop/no-widen-then-assert': 'error',
      'anti-slop/require-readable-spacing': 'error',
      'anti-slop/require-safety-comment-for-type-assertion': 'error',
    },
    overrides: [
      {
        files: ['resources/js/**/*.vue'],
        rules: {
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
          'anti-slop/no-unsafe-dictionary-type': 'off',
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
      'vendor/**',
      'node_modules/**',
      'public/**',
      'bootstrap/ssr/**',
      'tailwind.config.js',
      'resources/js/actions/**',
      'resources/js/components/ui/**',
      'resources/js/routes/**',
      'resources/js/wayfinder/**',
      'tools/oxlint/anti-slop/**',
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
});
