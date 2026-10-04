<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';

import { VueQueryDevtools } from '@tanstack/vue-query-devtools';

import AppLayout from '@/layouts/app/AppSidebarLayout.vue';

const { breadcrumbs = [] } = defineProps<{
  breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <VueQueryDevtools />

    <slot />
  </AppLayout>
</template>
