<script setup lang="ts">
import type { SelectFieldOption } from './SelectField.vue';

import { Check, ChevronsUpDown } from '@lucide/vue';
import { ComboboxVirtualizer } from 'reka-ui';
// --- Components --- //
import { computed, type HTMLAttributes, ref } from 'vue';

import {
  Combobox,
  ComboboxAnchor,
  ComboboxInput,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxList,
  ComboboxTrigger,
  ComboboxViewport,
} from '@/components/ui/combobox';
import { Label } from '@/components/ui/label';
import { useFieldIds } from '@/composables/useFieldIds';
import { cn } from '@/lib/utils';

import FieldFeedback from './FieldFeedback.vue';

// --- Props --- //

defineOptions({ inheritAttrs: false });

const model = defineModel<string | number>();

const props = withDefaults(
  defineProps<{
    name: string;
    label: string;
    options: SelectFieldOption[];
    id?: string;
    description?: string;
    error?: string;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    required?: boolean;
    hideLabel?: boolean;
    disabled?: boolean;
    defaultValue?: string | number;
    /**
     * Fixed row height in pixels. The virtualizer positions rows absolutely,
     * so every option must render at this height.
     */
    itemHeight?: number;
    class?: HTMLAttributes['class'];
    triggerClass?: HTMLAttributes['class'];
    ariaDescribedby?: string;
    ariaInvalid?: boolean | 'true' | 'false';
  }>(),
  {
    itemHeight: 32,
    searchPlaceholder: 'Search…',
    emptyText: 'No results found.',
  },
);

// --- Refs --- //

const searchTerm = ref('');

// --- Computed -- //

const triggerId = computed(() => props.id ?? props.name);

const optionsByValue = computed(
  () => new Map(props.options.map((option) => [option.value, option])),
);

/**
 * Labels are lowercased once per options change rather than on every
 * keystroke, which keeps filtering large lists cheap.
 */
const searchIndex = computed(() =>
  props.options.map((option) => ({
    value: option.value,
    searchText: option.label.toLocaleLowerCase(),
  })),
);

/**
 * Primitive values rather than option objects, so Reka can match the selected
 * model value and scroll to it when the list opens. Reka skips its own
 * filtering for virtualized lists, so matching happens here.
 */
const filteredValues = computed(() => {
  const term = searchTerm.value.toLocaleLowerCase();

  return searchIndex.value
    .filter((entry) => term === '' || entry.searchText.includes(term))
    .map((entry) => entry.value);
});

/**
 * Without v-model, defineModel keeps the selection locally and Reka falls
 * back to defaultValue, so the trigger does the same.
 */
const selectedLabel = computed(() => {
  const selectedValue = model.value ?? props.defaultValue;

  return selectedValue === undefined ? undefined : labelFor(selectedValue);
});

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: triggerId,
  description: () => props.description,
  error: () => props.error,
  ariaDescribedby: () => props.ariaDescribedby,
});

// --- Methods --- //

function labelFor(value: string | number): string | undefined {
  return optionsByValue.value.get(value)?.label;
}
</script>

<template>
  <div :class="cn('grid gap-2', props.class)" data-slot="search-select-field">
    <Label :for="triggerId" :class="cn('gap-1', hideLabel && 'sr-only')">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        *
      </span>
    </Label>

    <Combobox
      v-model="model"
      :name="name"
      :required="required"
      :disabled="disabled"
      :default-value="defaultValue"
    >
      <ComboboxAnchor as-child :class="cn('w-full', triggerClass)">
        <ComboboxTrigger
          :id="triggerId"
          :aria-label="undefined"
          :aria-describedby="describedBy"
          :aria-invalid="error ? 'true' : ariaInvalid"
          :data-placeholder="selectedLabel === undefined ? '' : undefined"
          class="flex h-9 items-center justify-between gap-2 rounded-md border border-input bg-transparent px-3 py-2 text-sm whitespace-nowrap shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 data-[placeholder]:text-muted-foreground dark:bg-input/30 dark:hover:bg-input/50 dark:aria-invalid:ring-destructive/40"
          v-bind="$attrs"
        >
          <span class="truncate">{{ selectedLabel ?? placeholder }}</span>
          <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
        </ComboboxTrigger>
      </ComboboxAnchor>

      <ComboboxList class="w-(--reka-combobox-trigger-width)" align="start">
        <ComboboxInput
          v-model="searchTerm"
          :display-value="() => ''"
          :placeholder="searchPlaceholder"
          :aria-label="label"
          :aria-describedby="describedBy"
        />

        <!-- Reka's ComboboxEmpty counts mounted items, which a virtualized list
        keeps at zero until rows are measured, so it would flash on open. -->
        <div
          v-if="filteredValues.length === 0"
          class="py-6 text-center text-sm"
          data-slot="combobox-empty"
        >
          {{ emptyText }}
        </div>

        <ComboboxViewport class="max-h-72 p-1">
          <ComboboxVirtualizer
            v-slot="{ option }"
            :options="filteredValues"
            :estimate-size="itemHeight"
            :text-content="(value) => labelFor(value) ?? ''"
          >
            <ComboboxItem
              :value="option"
              :disabled="optionsByValue.get(option)?.disabled"
              :style="{ height: `${itemHeight}px` }"
              class="w-full"
            >
              <span class="truncate">{{ labelFor(option) }}</span>
              <ComboboxItemIndicator>
                <Check />
              </ComboboxItemIndicator>
            </ComboboxItem>
          </ComboboxVirtualizer>
        </ComboboxViewport>
      </ComboboxList>
    </Combobox>

    <FieldFeedback
      :description="description"
      :description-id="descriptionId"
      :error="error"
      :error-id="errorId"
    />
  </div>
</template>
