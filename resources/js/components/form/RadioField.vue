<script setup lang="ts">
import { cn } from 'cn';
import { computed, type HTMLAttributes } from 'vue';

import {
  Field,
  FieldContent,
  FieldDescription,
  FieldLabel,
  FieldLegend,
  FieldSet,
} from '@/components/ui/field';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { useFieldIds } from '@/composables/useFieldIds';

import FieldFeedback from './FieldFeedback.vue';

// --- Types --- //

export interface RadioFieldOption {
  value: string | number;
  label: string;
  description?: string;
  disabled?: boolean;
}

// --- Props --- //

defineOptions({ inheritAttrs: false });

const model = defineModel<string | number>();

const props = defineProps<{
  name: string;
  /** Rendered as the fieldset legend. */
  label: string;
  /**
   * Rendered as radios unless the default slot supplies its own. With no
   * option chosen the form submits an empty string, which Laravel reads as null.
   */
  options?: RadioFieldOption[];
  /** Defaults to the name. Each option's radio gets `${id}-${value}`. */
  id?: string;
  description?: string;
  error?: string;
  hideLabel?: boolean;
  required?: boolean;
  disabled?: boolean;
  defaultValue?: string | number;
  /** Lays the options out and sets the arrow keys that move between them. */
  orientation?: 'vertical' | 'horizontal';
  class?: HTMLAttributes['class'];
  /** Styles the radio group itself. Layout classes for the whole field belong on `class`. */
  groupClass?: HTMLAttributes['class'];
  ariaDescribedby?: string;
  ariaInvalid?: boolean | 'true' | 'false';
}>();

// --- Computed -- //

const groupId = computed(() => props.id ?? props.name);

const isHorizontal = computed(() => props.orientation === 'horizontal');

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: groupId,
  description: () => props.description,
  error: () => props.error,
  ariaDescribedby: () => props.ariaDescribedby,
});

// --- Methods --- //

function optionId(option: RadioFieldOption): string {
  return `${groupId.value}-${option.value}`;
}
</script>

<template>
  <FieldSet
    :class="cn('gap-3', props.class)"
    :data-invalid="error ? true : undefined"
    :data-disabled="disabled ? true : undefined"
  >
    <FieldLegend
      variant="label"
      :class="
        cn(
          'flex gap-1',
          error && 'text-destructive',
          disabled && 'opacity-50',
          hideLabel && 'sr-only',
        )
      "
    >
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        *
      </span>
    </FieldLegend>

    <RadioGroup
      :id="groupId"
      v-model="model"
      :name="name"
      :default-value="defaultValue"
      :required="required"
      :disabled="disabled"
      :orientation="orientation"
      :class="cn(isHorizontal && 'flex flex-wrap gap-x-6', groupClass)"
      :aria-describedby="describedBy"
      :aria-invalid="error ? 'true' : ariaInvalid"
      v-bind="$attrs"
    >
      <slot>
        <Field
          v-for="option in options"
          :key="option.value"
          orientation="horizontal"
          :class="isHorizontal && 'w-auto'"
          :data-disabled="disabled || option.disabled ? true : undefined"
        >
          <RadioGroupItem
            :id="optionId(option)"
            :value="option.value"
            :disabled="option.disabled"
            :aria-invalid="error ? 'true' : undefined"
          />

          <FieldContent v-if="option.description">
            <FieldLabel :for="optionId(option)" class="font-normal">
              {{ option.label }}
            </FieldLabel>
            <FieldDescription>{{ option.description }}</FieldDescription>
          </FieldContent>

          <FieldLabel v-else :for="optionId(option)" class="font-normal">
            {{ option.label }}
          </FieldLabel>
        </Field>
      </slot>
    </RadioGroup>

    <FieldFeedback
      :description="description"
      :description-id="descriptionId"
      :error="error"
      :error-id="errorId"
    />
  </FieldSet>
</template>
