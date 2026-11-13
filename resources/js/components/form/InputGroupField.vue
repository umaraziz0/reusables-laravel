<script setup lang="ts">
import { cn } from 'cn';
import {
  computed,
  type HTMLAttributes,
  type InputTypeHTMLAttribute,
} from 'vue';

import { Field, FieldLabel } from '@/components/ui/field';
import {
  InputGroup,
  InputGroupAddon,
  InputGroupInput,
} from '@/components/ui/input-group';
import { useFieldIds } from '@/composables/useFieldIds';

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
  /**
   * Also dims the addons, which a disabled attribute on the input cannot reach.
   */
  disabled?: boolean;
  defaultValue?: string | number;
  class?: HTMLAttributes['class'];
  /** Styles the group itself. Set widths on `class`: Field forces its children to full width. */
  groupClass?: HTMLAttributes['class'];
  inputClass?: HTMLAttributes['class'];
  ariaDescribedby?: string;
  ariaInvalid?: boolean | 'true' | 'false';
  type?: InputTypeHTMLAttribute;
}>();

// --- Computed -- //

const inputId = computed(() => props.id ?? props.name);

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: inputId,
  description: () => props.description,
  error: () => props.error,
  ariaDescribedby: () => props.ariaDescribedby,
});
</script>

<template>
  <Field :class="props.class" :data-invalid="error ? true : undefined">
    <FieldLabel :for="inputId" :class="cn('gap-1', hideLabel && 'sr-only')">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        *
      </span>
    </FieldLabel>

    <InputGroup :class="groupClass" :data-disabled="disabled || undefined">
      <InputGroupInput
        :id="inputId"
        v-model="model"
        :name="name"
        :class="inputClass"
        :aria-describedby="describedBy"
        :aria-invalid="error ? 'true' : ariaInvalid"
        :required="required"
        :disabled="disabled"
        :type="type"
        :default-value="defaultValue"
        v-bind="$attrs"
      />

      <InputGroupAddon v-if="$slots['block-start']" align="block-start">
        <slot name="block-start" />
      </InputGroupAddon>

      <InputGroupAddon v-if="$slots.start" align="inline-start">
        <slot name="start" />
      </InputGroupAddon>

      <InputGroupAddon v-if="$slots.end" align="inline-end">
        <slot name="end" />
      </InputGroupAddon>

      <InputGroupAddon v-if="$slots['block-end']" align="block-end">
        <slot name="block-end" />
      </InputGroupAddon>
    </InputGroup>

    <FieldFeedback
      :description="description"
      :description-id="descriptionId"
      :error="error"
      :error-id="errorId"
    />
  </Field>
</template>
