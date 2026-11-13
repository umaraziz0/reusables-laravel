<script setup lang="ts">
import { cn } from 'cn';
import {
  computed,
  ref,
  type HTMLAttributes,
  type InputTypeHTMLAttribute,
} from 'vue';

import { Field, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
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
  defaultValue?: string | number;
  class?: HTMLAttributes['class'];
  /** Styles the input itself. Set widths on `class`: Field forces its children to full width. */
  inputClass?: HTMLAttributes['class'];
  ariaDescribedby?: string;
  ariaInvalid?: boolean | 'true' | 'false';
  type?: InputTypeHTMLAttribute;
}>();

// --- Refs --- //

const emailSuggestion = ref<string>();

// --- Computed -- //

const emailSpellingListeners = computed(() =>
  props.type === 'email'
    ? { blur: checkEmailSpelling, input: clearEmailSuggestion }
    : {},
);

const inputId = computed(() => props.id ?? props.name);

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: inputId,
  description: () => emailSuggestion.value ?? props.description,
  error: () => props.error,
  ariaDescribedby: () => props.ariaDescribedby,
});

// --- Methods --- //

/**
 * Lazy-loads the spell checker so only email inputs pay for it.
 */
async function checkEmailSpelling(event: FocusEvent): Promise<void> {
  if (!(event.target instanceof HTMLInputElement)) {
    return;
  }

  const input = event.target;
  const email = input.value.trim();

  if (!email) {
    clearEmailSuggestion();

    return;
  }

  const { run } = await import('@zootools/email-spell-checker');

  if (input.value.trim() !== email) {
    return;
  }

  emailSuggestion.value = run({ email })?.full;
}

function clearEmailSuggestion(): void {
  emailSuggestion.value = undefined;
}

function applyEmailSuggestion(): void {
  model.value = emailSuggestion.value;
  clearEmailSuggestion();
}
</script>

<template>
  <Field :class="props.class" :data-invalid="error ? true : undefined">
    <FieldLabel :for="inputId" :class="cn('gap-1', hideLabel && 'sr-only')">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        *
      </span>
    </FieldLabel>

    <Input
      :id="inputId"
      v-model="model"
      :name="name"
      :class="inputClass"
      :aria-describedby="describedBy"
      :aria-invalid="error ? 'true' : ariaInvalid"
      :required="required"
      :type="type"
      :default-value="defaultValue"
      v-bind="$attrs"
      v-on="emailSpellingListeners"
    />

    <FieldFeedback
      :description="description"
      :description-id="descriptionId"
      :error="error"
      :error-id="errorId"
    >
      <template v-if="emailSuggestion" #default>
        <span role="status">
          Did you mean
          <button
            type="button"
            class="font-medium text-foreground underline underline-offset-4 hover:text-primary"
            @click="applyEmailSuggestion"
          >
            {{ emailSuggestion }}</button
          >?
        </span>
      </template>
    </FieldFeedback>
  </Field>
</template>
