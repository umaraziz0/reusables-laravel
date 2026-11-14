<script setup lang="ts">
import { useDebounceFn } from '@vueuse/core';
import { cn } from 'cn';
import { computed, ref, type HTMLAttributes } from 'vue';

import { Field, FieldLabel } from '@/components/ui/field';
import { Textarea } from '@/components/ui/textarea';
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
  disabled?: boolean;
  defaultValue?: string | number;
  class?: HTMLAttributes['class'];
  /** Styles the textarea itself. Set widths on `class`: Field forces its children to full width. */
  textareaClass?: HTMLAttributes['class'];
  ariaDescribedby?: string;
  ariaInvalid?: boolean | 'true' | 'false';
  /** Caps the length natively and shows a character counter. */
  maxlength?: number;
}>();

// --- Constants --- //

/** Waits for a pause in typing so screen readers aren't interrupted on every keystroke. */
const ANNOUNCE_DELAY_MS = 500;

// --- Refs --- //

const remainingAnnouncement = ref('');

// --- Computed -- //

const textareaId = computed(() => props.id ?? props.name);

const characterCount = computed(
  () => String(model.value ?? props.defaultValue ?? '').length,
);

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: textareaId,
  description: () => props.description,
  error: () => props.error,
  ariaDescribedby: () => props.ariaDescribedby,
});

// --- Methods --- //

const announceRemaining = useDebounceFn((): void => {
  if (props.maxlength === undefined) {
    return;
  }

  const remaining = props.maxlength - characterCount.value;

  remainingAnnouncement.value = `${remaining} ${remaining === 1 ? 'character' : 'characters'} remaining`;
}, ANNOUNCE_DELAY_MS);
</script>

<template>
  <Field
    :class="props.class"
    :data-invalid="error ? true : undefined"
    :data-disabled="disabled ? true : undefined"
  >
    <FieldLabel :for="textareaId" :class="cn('gap-1', hideLabel && 'sr-only')">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        *
      </span>
    </FieldLabel>

    <Textarea
      :id="textareaId"
      v-model="model"
      :name="name"
      :class="textareaClass"
      :aria-describedby="describedBy"
      :aria-invalid="error ? 'true' : ariaInvalid"
      :required="required"
      :disabled="disabled"
      :default-value="defaultValue"
      :maxlength="maxlength"
      v-bind="$attrs"
      @input="announceRemaining"
    />

    <div
      v-if="description || error || maxlength !== undefined"
      class="flex items-start justify-between gap-3"
    >
      <FieldFeedback
        :description="description"
        :description-id="descriptionId"
        :error="error"
        :error-id="errorId"
      />

      <template v-if="maxlength !== undefined">
        <p
          data-slot="field-counter"
          aria-hidden="true"
          class="ml-auto shrink-0 text-sm text-muted-foreground tabular-nums"
        >
          {{ characterCount }} / {{ maxlength }}
        </p>

        <span role="status" class="sr-only">{{ remainingAnnouncement }}</span>
      </template>
    </div>
  </Field>
</template>
