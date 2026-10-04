import type { Appearance, ResolvedAppearance } from '@/types';
import type { ComputedRef, Ref } from 'vue';

import { computed, onMounted, ref } from 'vue';

export type { Appearance, ResolvedAppearance } from '@/types';

export type UseAppearanceReturn = {
  appearance: Ref<Appearance>;
  resolvedAppearance: ComputedRef<ResolvedAppearance>;
  updateAppearance: (value: Appearance) => void;
};

function isBrowser(): boolean {
  return globalThis.window !== undefined;
}

function parseAppearance(value: string | null): Appearance | null {
  switch (value) {
    case 'light':
    case 'dark':
    case 'system': {
      return value;
    }

    default: {
      return null;
    }
  }
}

export function updateTheme(value: Appearance): void {
  if (!isBrowser()) {
    return;
  }

  if (value === 'system') {
    const mediaQueryList = globalThis.window.matchMedia(
      '(prefers-color-scheme: dark)',
    );

    const systemTheme = mediaQueryList.matches ? 'dark' : 'light';

    document.documentElement.classList.toggle('dark', systemTheme === 'dark');
  } else {
    document.documentElement.classList.toggle('dark', value === 'dark');
  }
}

const setCookie = (name: string, value: string, days = 365) => {
  if (globalThis.document === undefined) {
    return;
  }

  const maxAge = days * 24 * 60 * 60;

  document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const mediaQuery = () => {
  if (!isBrowser()) {
    return null;
  }

  return globalThis.window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = () => {
  if (!isBrowser()) {
    return null;
  }

  return parseAppearance(localStorage.getItem('appearance'));
};

const prefersDark = (): boolean => {
  if (!isBrowser()) {
    return false;
  }

  return globalThis.window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const handleSystemThemeChange = () => {
  const currentAppearance = getStoredAppearance();

  updateTheme(currentAppearance || 'system');
};

export function initializeTheme(): void {
  if (!isBrowser()) {
    return;
  }

  // Initialize theme from saved preference or default to system...
  const savedAppearance = getStoredAppearance();
  updateTheme(savedAppearance || 'system');

  // Set up system theme change listener...
  mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

const appearance = ref<Appearance>('system');

export function useAppearance(): UseAppearanceReturn {
  onMounted(() => {
    const savedAppearance = getStoredAppearance();

    if (savedAppearance) {
      appearance.value = savedAppearance;
    }
  });

  const resolvedAppearance = computed<ResolvedAppearance>(() => {
    if (appearance.value === 'system') {
      return prefersDark() ? 'dark' : 'light';
    }

    return appearance.value;
  });

  function updateAppearance(value: Appearance) {
    appearance.value = value;

    // Store in localStorage for client-side persistence...
    localStorage.setItem('appearance', value);

    // Store in cookie for SSR...
    setCookie('appearance', value);

    updateTheme(value);
  }

  return {
    appearance,
    resolvedAppearance,
    updateAppearance,
  };
}
