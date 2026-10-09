<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue';
// --- Components --- //

import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';

import FieldFeedback from './FieldFeedback.vue';

// --- Types --- //

export interface SelectFieldOption {
  value: string | number;
  label: string;
  disabled?: boolean;
}

// --- Props --- //

defineOptions({ inheritAttrs: false });

const model = defineModel<string | number>();

const props = defineProps<{
  name: string;
  label: string;
  /**
   * Rendered as items unless the default slot supplies its own. Reka reserves
   * an empty string for clearing, so leave the model undefined for "no
   * selection" and use the placeholder instead.
   */
  options?: SelectFieldOption[];
  id?: string;
  description?: string;
  error?: string;
  placeholder?: string;
  hideLabel?: boolean;
  required?: boolean;
  disabled?: boolean;
  defaultValue?: string | number;
  class?: HTMLAttributes['class'];
  triggerClass?: HTMLAttributes['class'];
  ariaDescribedby?: string;
  ariaInvalid?: boolean | 'true' | 'false';
}>();

// --- Computed -- //

const selectId = computed(() => props.id ?? props.name);

const descriptionId = computed(() =>
  props.description ? `${selectId.value}-description` : undefined,
);

const errorId = computed(() =>
  props.error ? `${selectId.value}-error` : undefined,
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
  <div :class="cn('grid gap-2', props.class)" data-slot="select-field">
    <Label :for="selectId" :class="hideLabel ? 'sr-only' : undefined">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        {{ ' *' }}
      </span>
    </Label>

    <Select
      v-model="model"
      :name="name"
      :required="required"
      :disabled="disabled"
      :default-value="defaultValue"
    >
      <SelectTrigger
        :id="selectId"
        :class="cn('w-full', triggerClass)"
        :aria-describedby="describedBy"
        :aria-invalid="error ? 'true' : ariaInvalid"
        v-bind="$attrs"
      >
        <SelectValue :placeholder="placeholder" />
      </SelectTrigger>

      <SelectContent>
        <slot>
          <SelectItem
            v-for="option in options"
            :key="option.value"
            :value="option.value"
            :disabled="option.disabled"
          >
            {{ option.label }}
          </SelectItem>
        </slot>
      </SelectContent>
    </Select>

    <FieldFeedback
      :description="description"
      :description-id="descriptionId"
      :error="error"
      :error-id="errorId"
    />
  </div>
</template>
