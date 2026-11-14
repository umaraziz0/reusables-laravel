<script setup lang="ts">
import { cn } from 'cn';
import { computed, type HTMLAttributes } from 'vue';

import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldContent, FieldLabel } from '@/components/ui/field';
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
  /** Submitted when checked. Defaults to "1" so Laravel's `boolean` rule accepts it. */
  value?: string;
  class?: HTMLAttributes['class'];
  /** Styles the checkbox itself. Layout classes belong on `class`. */
  checkboxClass?: HTMLAttributes['class'];
  ariaDescribedby?: string;
  ariaInvalid?: boolean | 'true' | 'false';
}>();

// --- Computed -- //

const checkboxId = computed(() => props.id ?? props.name);

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: checkboxId,
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
    <Checkbox
      :id="checkboxId"
      v-model="model"
      :name="name"
      :default-value="defaultValue"
      :value="value ?? '1'"
      :class="checkboxClass"
      :aria-describedby="describedBy"
      :aria-invalid="error ? 'true' : ariaInvalid"
      :required="required"
      :disabled="disabled"
      v-bind="$attrs"
    />

    <FieldContent>
      <FieldLabel
        :for="checkboxId"
        :class="cn('gap-1 font-normal', hideLabel && 'sr-only')"
      >
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
  </Field>
</template>
