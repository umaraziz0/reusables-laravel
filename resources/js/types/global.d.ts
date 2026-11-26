import type { Auth } from '@/types/auth';
import type { FlashToast } from '@/types/ui';
import type { createHeadManager, Page, Router } from '@inertiajs/core';
import type { Directive } from 'vue';

declare global {
  /** Whether component preview pages are bundled; set by vite.config.ts. */
  const __COMPONENT_PREVIEWS__: boolean;

  /** Vite declares ImportMetaEnv globally, so app variables extend it here. */
  interface ImportMetaEnv {
    readonly VITE_APP_NAME: string;
  }
}

declare module '@inertiajs/core' {
  export interface InertiaConfig {
    sharedPageProps: {
      name: string;
      auth: Auth;
      sidebarOpen: boolean;
      quote?: { message: string; author: string };
      [key: string]: unknown;
    };
    flashDataType: {
      toast?: FlashToast;
    };
  }
}

declare module 'vue' {
  interface GlobalDirectives {
    vFocus: Directive<HTMLElement, boolean | undefined>;
  }

  interface ComponentCustomProperties {
    $inertia: Router;
    $page: Page;
    $headManager: ReturnType<typeof createHeadManager>;
  }
}
