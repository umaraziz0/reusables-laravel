<script setup lang="ts">
import { FieldDescription, FieldError } from '@/components/ui/field';

// --- Props --- //

defineProps<{
  description?: string;
  descriptionId?: string;
  error?: string;
  errorId?: string;
}>();
</script>

<template>
  <FieldError v-if="error" :id="errorId">{{ error }}</FieldError>

  <FieldDescription
    v-else-if="description || $slots.default"
    :id="descriptionId"
  >
    <slot>{{ description }}</slot>
  </FieldDescription>
</template>
