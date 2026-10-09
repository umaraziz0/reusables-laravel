<script setup lang="ts">
import { computed } from 'vue';

// --- Props --- //

const props = defineProps<{
  description?: string;
  descriptionId?: string;
  error?: string;
  errorId?: string;
}>();

// --- Constants --- //

const feedback = computed(() => props.error || props.description);

const feedbackId = computed(() =>
  props.error ? props.errorId : props.descriptionId,
);
</script>

<template>
  <p
    v-if="feedback || $slots.default"
    :id="feedbackId"
    :class="
      error ? 'text-sm text-destructive' : 'text-sm text-muted-foreground'
    "
    :role="error ? 'alert' : undefined"
  >
    <slot>{{ feedback }}</slot>
  </p>
</template>
