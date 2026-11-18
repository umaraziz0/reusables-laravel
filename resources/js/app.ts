import type { DefineComponent } from 'vue';

import { createInertiaApp } from '@inertiajs/vue3';

import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Component preview pages set their own layout and stay out of the bundle
// when component previews are off (see `app.component_previews`).
const pages = __COMPONENT_PREVIEWS__
  ? import.meta.glob<DefineComponent>('./pages/**/*.vue', { import: 'default' })
  : import.meta.glob<DefineComponent>(
      ['./pages/**/*.vue', '!./pages/components/**'],
      { import: 'default' },
    );

void createInertiaApp({
  resolve: async (name) => {
    const page = pages[`./pages/${name}.vue`];

    if (!page) {
      throw new Error(`Page not found: ${name}`);
    }

    return page();
  },
  title: (title) => (title ? `${title} - ${appName}` : appName),
  layout: (name) => {
    switch (true) {
      case name === 'Welcome':
        return null;
      case name.startsWith('auth/'):
        return AuthLayout;
      case name.startsWith('settings/'):
        return [AppLayout, SettingsLayout];
      default:
        return AppLayout;
    }
  },
  progress: {
    color: '#4B5563',
  },
  withApp: (app) => {
    app.directive('focus', {
      mounted: (el: HTMLElement, shouldFocus) => {
        if (shouldFocus.value !== false) {
          el.focus();
        }
      },
    });
  },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
