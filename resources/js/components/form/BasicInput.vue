<script setup lang="ts">
import { computed, useSlots, type HTMLAttributes } from 'vue';

import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

import InputFeedback from './InputFeedback.vue';

defineOptions({ inheritAttrs: false });

const model = defineModel<string | number>();

const props = defineProps<{
  name: string;
  label?: string;
  id?: string;
  description?: string;
  error?: string;
  hideLabel?: boolean;
  required?: boolean;
  defaultValue?: string | number;
  class?: HTMLAttributes['class'];
  inputClass?: HTMLAttributes['class'];
  ariaDescribedby?: string;
  ariaInvalid?: boolean | 'true' | 'false';
}>();

const slots = useSlots();

const inputId = computed(() => props.id ?? props.name);

const hasError = computed(() => Boolean(props.error?.trim()));

const feedbackId = computed(() => {
  if (hasError.value) {
    return `${inputId.value}-error`;
  }

  if (props.description?.trim() || slots.description) {
    return `${inputId.value}-description`;
  }

  return '';
});

const describedBy = computed(() =>
  [props.ariaDescribedby, feedbackId.value].filter(Boolean).join(' '),
);
</script>

<template>
  <div :class="cn('grid gap-2', props.class)" data-slot="input-field">
    <Label :for="inputId" :class="hideLabel && 'sr-only'">
      <slot name="label">{{ label }}</slot>
      <span v-if="required" aria-hidden="true" class="text-destructive">
        *
      </span>
    </Label>

    <Input
      :id="inputId"
      :name="name"
      v-bind="$attrs"
      v-model="model"
      :default-value="defaultValue"
      :required="required"
      :aria-invalid="hasError ? true : ariaInvalid"
      :aria-describedby="describedBy || undefined"
      :class="inputClass"
    />

    <InputFeedback
      :description="description"
      :description-id="`${inputId}-description`"
      :error="error"
      :error-id="`${inputId}-error`"
    >
      <template v-if="$slots.description">
        <slot name="description" />
      </template>
    </InputFeedback>
  </div>
</template>
