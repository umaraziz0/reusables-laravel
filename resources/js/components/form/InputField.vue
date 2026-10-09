<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue';
// --- Components --- //

import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

import FieldFeedback from './FieldFeedback.vue';

// --- Props --- //

defineOptions({ inheritAttrs: false });

const model = defineModel<string | number>();

const props = defineProps<{
  name: string;
  label: string;
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

// --- Constants --- //

const inputId = computed(() => props.id ?? props.name);

const descriptionId = computed(() =>
  props.description ? `${inputId.value}-description` : undefined,
);

const errorId = computed(() =>
  props.error ? `${inputId.value}-error` : undefined,
);

const feedbackId = computed(() =>
  props.error ? errorId.value : descriptionId.value,
);

const describedBy = computed(
  () =>
    [props.ariaDescribedby, feedbackId.value].filter(Boolean).join(' ') ||
    undefined,
);
</script>

<template>
  <div :class="cn('grid gap-2', props.class)" data-slot="input-field">
    <Label :for="inputId" :class="hideLabel ? 'sr-only' : undefined">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        {{ ' *' }}
      </span>
    </Label>

    <Input
      :id="inputId"
      v-model="model"
      :name="name"
      :class="inputClass"
      :aria-describedby="describedBy"
      :aria-invalid="error ? 'true' : ariaInvalid"
      :required="required"
      :default-value="defaultValue"
      v-bind="$attrs"
    />

    <FieldFeedback
      :description="description"
      :description-id="descriptionId"
      :error="error"
      :error-id="errorId"
    />
  </div>
</template>
