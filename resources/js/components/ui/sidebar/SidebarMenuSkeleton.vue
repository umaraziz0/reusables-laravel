<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "cn"
import { computed } from "vue"
import { Skeleton } from '@/components/ui/skeleton'

const props = defineProps<{
  showIcon?: boolean
  class?: HTMLAttributes["class"]
}>()

const width = computed(() => {
  return `${Math.floor(Math.random() * 40) + 50}%`
})
</script>

<template>
  <div
    data-slot="sidebar-menu-skeleton"
    data-sidebar="menu-skeleton"
    :class="cn('flex h-8 items-center gap-2 rounded-md px-2', props.class)"
  >
    <Skeleton
      v-if="showIcon"
      class="size-4 rounded-md"
      data-sidebar="menu-skeleton-icon"
    />

    <Skeleton
      class="skeleton-width h-4 flex-1"
      data-sidebar="menu-skeleton-text"
    />
  </div>
</template>

<style scoped>
.skeleton-width {
  max-width: v-bind(width);
}
</style>
