<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { useAppPage } from '@/composables/useAppPage';

interface Props {
  variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = useAppPage().props.sidebarOpen;
</script>

<template>
  <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
    <slot />
  </div>
  <SidebarProvider v-else :default-open="isOpen">
    <slot />
  </SidebarProvider>
</template>
