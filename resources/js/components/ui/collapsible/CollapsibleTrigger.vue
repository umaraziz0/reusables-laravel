<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { CollapsibleTrigger, type CollapsibleTriggerProps } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<
  CollapsibleTriggerProps & { class?: HTMLAttributes['class'] }
>();

const delegatedProps = reactiveOmit(props, 'class');
</script>

<template>
  <CollapsibleTrigger
    data-slot="collapsible-trigger"
    :class="
      cn(
        'flex w-full items-center justify-between px-4 py-3 text-left font-medium hover:bg-accent hover:text-accent-foreground [&[data-state=open]>svg]:rotate-180',
        props.class,
      )
    "
    v-bind="delegatedProps"
  >
    <slot />
  </CollapsibleTrigger>
</template>
