<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  description?: string;
  descriptionId?: string;
  error?: string;
  errorId?: string;
}>();

const hasError = computed(() => Boolean(props.error?.trim()));
</script>

<template>
  <p
    v-if="hasError || description?.trim() || $slots.default"
    :id="hasError ? errorId : descriptionId"
    :class="
      hasError ? 'text-sm text-destructive' : 'text-sm text-muted-foreground'
    "
    :role="hasError ? 'alert' : undefined"
  >
    <template v-if="hasError">{{ error }}</template>
    <slot v-else>{{ description }}</slot>
  </p>
</template>
