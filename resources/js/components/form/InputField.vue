<script setup lang="ts">
import {
  computed,
  ref,
  type HTMLAttributes,
  type InputTypeHTMLAttribute,
} from 'vue';
// --- Components --- //

import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useFieldIds } from '@/composables/useFieldIds';
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

const feedbackDescription = computed(() =>
  emailSuggestion.value
    ? `Did you mean ${emailSuggestion.value}?`
    : props.description,
);

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: inputId,
  description: feedbackDescription,
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
  <div :class="cn('grid gap-2', props.class)" data-slot="input-field">
    <Label :for="inputId" :class="cn('gap-1', hideLabel && 'sr-only')">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        *
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
      :type="type"
      :default-value="defaultValue"
      v-bind="$attrs"
      v-on="emailSpellingListeners"
    />

    <FieldFeedback
      :description="feedbackDescription"
      :description-id="descriptionId"
      :error="error"
      :error-id="errorId"
    >
      <template v-if="emailSuggestion && !error" #default>
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
  </div>
</template>
