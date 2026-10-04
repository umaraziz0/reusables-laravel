<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';

import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import { Toaster } from '@/components/ui/sonner';

type Props = {
  breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});
</script>

<template>
  <AppShell variant="header">
    <AppHeader :breadcrumbs="breadcrumbs" />
    <AppContent variant="header">
      <slot />
    </AppContent>
    <Toaster />
  </AppShell>
</template>
