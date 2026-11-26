<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

import { cn } from 'cn';
import { computed } from 'vue';

import { Field, FieldContent, FieldLabel } from '@/components/ui/field';
import { Switch } from '@/components/ui/switch';
import { useFieldIds } from '@/composables/useFieldIds';

import FieldFeedback from './FieldFeedback.vue';

// --- Props --- //

defineOptions({ inheritAttrs: false });

/** Defaults to undefined: an absent boolean model would cast to false and override `defaultValue`. */
const model = defineModel<boolean | undefined>({ default: undefined });

const props = defineProps<{
  name: string;
  label: string;
  id?: string;
  description?: string;
  error?: string;
  hideLabel?: boolean;
  required?: boolean;
  disabled?: boolean;
  defaultValue?: boolean;
  /** Submitted when on. Defaults to "1" so Laravel's `boolean` rule accepts it. */
  value?: string;
  class?: HTMLAttributes['class'];
  /** Styles the switch itself. Layout classes belong on `class`. */
  switchClass?: HTMLAttributes['class'];
  ariaDescribedby?: string;
  ariaInvalid?: boolean | 'true' | 'false';
}>();

// --- Computed -- //

const switchId = computed(() => props.id ?? props.name);

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: switchId,
  description: () => props.description,
  error: () => props.error,
  ariaDescribedby: () => props.ariaDescribedby,
});
</script>

<template>
  <Field
    orientation="horizontal"
    :class="props.class"
    :data-invalid="error ? true : undefined"
    :data-disabled="disabled ? true : undefined"
  >
    <FieldContent>
      <FieldLabel :for="switchId" :class="cn('gap-1', hideLabel && 'sr-only')">
        {{ label }}
        <span v-if="required" aria-hidden="true" class="text-destructive">
          *
        </span>
      </FieldLabel>

      <FieldFeedback
        :description="description"
        :description-id="descriptionId"
        :error="error"
        :error-id="errorId"
      />
    </FieldContent>

    <Switch
      :id="switchId"
      v-model="model"
      :name="name"
      :default-value="defaultValue"
      :value="value ?? '1'"
      :class="switchClass"
      :aria-describedby="describedBy"
      :aria-invalid="error ? 'true' : ariaInvalid"
      :required="required"
      :disabled="disabled"
      v-bind="$attrs"
    />
  </Field>
</template>
