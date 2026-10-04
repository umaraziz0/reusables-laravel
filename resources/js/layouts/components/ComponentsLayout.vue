<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';

import { Toaster } from '@/components/ui/sonner';
import ComponentsContent from '@/layouts/components/ComponentsContent.vue';
import ComponentsShell from '@/layouts/components/ComponentsShell.vue';
import ComponentsSidebar from '@/layouts/components/ComponentsSidebar.vue';
import ComponentsSidebarHeader from '@/layouts/components/ComponentsSidebarHeader.vue';

const { breadcrumbs = [] } = defineProps<{
  breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
  <ComponentsShell variant="sidebar">
    <ComponentsSidebar />
    <ComponentsContent
      variant="sidebar"
      class="flex flex-col gap-y-6 overflow-x-hidden px-4 py-6"
    >
      <ComponentsSidebarHeader :breadcrumbs="breadcrumbs" />
      <slot />
    </ComponentsContent>
    <Toaster />
  </ComponentsShell>
</template>
