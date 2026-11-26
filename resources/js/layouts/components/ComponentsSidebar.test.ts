import { enableAutoUnmount, mount } from '@vue/test-utils';
import { afterEach, describe, expect, it, vi } from 'vite-plus/test';
import { defineComponent, h, reactive } from 'vue';

import { SidebarProvider } from '@/components/ui/sidebar';

import ComponentsSidebar from './ComponentsSidebar.vue';

const page = vi.hoisted(() => ({ url: '/' }));

// oxlint-disable-next-line landmines/no-test-doubles -- The real usePage and Link need a booted Inertia app; the sidebar logic under test stays real.
vi.mock('@inertiajs/vue3', async (importOriginal) => ({
  // oxlint-disable-next-line typescript/consistent-type-imports -- Vitest's importOriginal needs the module type; a namespace import trips import/no-namespace.
  ...(await importOriginal<typeof import('@inertiajs/vue3')>()),
  usePage: () => reactive(page),
  /** Renders a plain anchor so the test needs no Inertia router. */
  Link: defineComponent({
    props: { href: { type: [String, Object], required: true } },
    setup:
      (_, { slots }) =>
      () =>
        h('a', slots.default?.()),
  }),
}));

enableAutoUnmount(afterEach);

/**
 * Mounts the sidebar as it renders on the page at the given path. The URL is
 * set through the reactive proxy because `useCurrentUrl` reads the page once,
 * when its module loads.
 */
function mountOnPage(url: string): ReturnType<typeof mount> {
  reactive(page).url = url;

  return mount(() => h(SidebarProvider, () => h(ComponentsSidebar)), {
    attachTo: document.body,
  });
}

describe('ComponentsSidebar', () => {
  it('opens the folder that contains the current page', () => {
    const wrapper = mountOnPage('/components/form/select-field');

    expect(wrapper.text()).toContain('Input Group Field');
  });

  it('keeps folders closed when none of their pages is current', () => {
    const wrapper = mountOnPage('/components/buttons');

    expect(wrapper.text()).toContain('Form');
    expect(wrapper.text()).not.toContain('Input Group Field');
  });
});
