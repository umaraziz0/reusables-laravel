<script setup lang="ts">
import { Check, ChevronDown } from '@lucide/vue';
import { cn } from 'cn';
import { ComboboxVirtualizer } from 'reka-ui';
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
import {
  InputGroup,
  InputGroupAddon,
  InputGroupButton,
  InputGroupInput,
} from '@/components/ui/input-group';
import { Label } from '@/components/ui/label';
import { useFieldIds } from '@/composables/useFieldIds';
import {
  COUNTRIES,
  type Country,
  PRIMARY_COUNTRY_BY_DIAL_CODE,
} from '@/lib/countries';

import FieldFeedback from './FieldFeedback.vue';

// --- Props --- //

defineOptions({ inheritAttrs: false });

/**
 * The national number, without the dial code.
 */
const model = defineModel<string>();

/**
 * The selected dial code, e.g. `+62`.
 */
const dialCode = defineModel<string>('dialCode');

const props = withDefaults(
  defineProps<{
    name: string;
    label: string;
    /**
     * Submitted name of the dial code. Defaults to `{name}_dial_code`.
     */
    dialCodeName?: string;
    id?: string;
    description?: string;
    error?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    hideLabel?: boolean;
    required?: boolean;
    disabled?: boolean;
    defaultValue?: string;
    defaultDialCode?: string;
    countries?: readonly Country[];
    /**
     * Fixed row height in pixels. The virtualizer positions rows absolutely,
     * so every option must render at this height.
     */
    itemHeight?: number;
    class?: HTMLAttributes['class'];
    groupClass?: HTMLAttributes['class'];
    inputClass?: HTMLAttributes['class'];
    ariaDescribedby?: string;
    ariaInvalid?: boolean | 'true' | 'false';
  }>(),
  {
    defaultDialCode: '+62',
    countries: () => COUNTRIES,
    itemHeight: 32,
    searchPlaceholder: 'Search country or code…',
    emptyText: 'No countries found.',
  },
);

// --- Refs --- //

const searchTerm = ref('');

/**
 * The country the user picked. Several countries share a dial code, so this
 * keeps the flag the user chose while the dial code still matches.
 */
const pickedCountryCode = ref<string>();

// --- Computed -- //

const inputId = computed(() => props.id ?? props.name);

const submittedDialCodeName = computed(
  () => props.dialCodeName ?? `${props.name}_dial_code`,
);

const countriesByCode = computed(
  () => new Map(props.countries.map((country) => [country.code, country])),
);

/**
 * Without v-model:dial-code, defineModel keeps the selection locally and the
 * field falls back to defaultDialCode.
 */
const selectedDialCode = computed(
  () => dialCode.value ?? props.defaultDialCode,
);

const selectedCountry = computed(() => {
  const picked = pickedCountryCode.value
    ? countriesByCode.value.get(pickedCountryCode.value)
    : undefined;

  if (picked?.dialCode === selectedDialCode.value) {
    return picked;
  }

  const primaryCode = PRIMARY_COUNTRY_BY_DIAL_CODE.get(selectedDialCode.value);

  return (
    (primaryCode ? countriesByCode.value.get(primaryCode) : undefined) ??
    props.countries.find(
      (country) => country.dialCode === selectedDialCode.value,
    )
  );
});

/**
 * Picking a country stores it so the flag stays put, and emits only its dial
 * code.
 */
const selectedCountryCode = computed({
  get: () => selectedCountry.value?.code,
  set: (code) => {
    const country = code ? countriesByCode.value.get(code) : undefined;

    if (!country) {
      return;
    }

    pickedCountryCode.value = country.code;
    dialCode.value = country.dialCode;
  },
});

const triggerLabel = computed(() =>
  selectedCountry.value
    ? `Country code: ${selectedCountry.value.name} ${selectedCountry.value.dialCode}`
    : 'Country code',
);

/**
 * Search text is built once per countries change rather than on every
 * keystroke. It covers the name, ISO code and dial code, so "indo", "id",
 * "62" and "+62" all find Indonesia.
 */
const searchIndex = computed(() =>
  props.countries.map((country) => ({
    code: country.code,
    searchText:
      `${country.name} ${country.code} ${country.dialCode}`.toLocaleLowerCase(),
  })),
);

/**
 * Reka skips its own filtering for virtualized lists, so matching happens
 * here. Items are keyed by ISO code because dial codes are not unique.
 */
const filteredCodes = computed(() => {
  const term = searchTerm.value.trim().toLocaleLowerCase();

  return searchIndex.value
    .filter((entry) => term === '' || entry.searchText.includes(term))
    .map((entry) => entry.code);
});

// --- Hooks --- //

const { descriptionId, errorId, describedBy } = useFieldIds({
  id: inputId,
  description: () => props.description,
  error: () => props.error,
  ariaDescribedby: () => props.ariaDescribedby,
});
</script>

<template>
  <div :class="cn('grid gap-2', props.class)" data-slot="phone-number-field">
    <Label :for="inputId" :class="cn('gap-1', hideLabel && 'sr-only')">
      {{ label }}
      <span v-if="required" aria-hidden="true" class="text-destructive">
        *
      </span>
    </Label>

    <Combobox v-model="selectedCountryCode" :disabled="disabled">
      <ComboboxAnchor as-child :class="cn('w-full', groupClass)">
        <InputGroup :data-disabled="disabled || undefined">
          <InputGroupAddon align="inline-start">
            <ComboboxTrigger as-child>
              <InputGroupButton
                :aria-label="triggerLabel"
                :aria-describedby="describedBy"
                :aria-invalid="error ? 'true' : ariaInvalid"
                :disabled="disabled"
                class="cursor-pointer text-foreground"
                data-slot="phone-number-field-trigger"
              >
                <span v-if="selectedCountry" aria-hidden="true">
                  {{ selectedCountry.flag }}
                </span>
                <span class="tabular-nums">{{ selectedDialCode }}</span>
                <ChevronDown class="opacity-50" />
              </InputGroupButton>
            </ComboboxTrigger>
          </InputGroupAddon>

          <InputGroupInput
            :id="inputId"
            v-model="model"
            :name="name"
            type="tel"
            inputmode="tel"
            autocomplete="tel-national"
            :class="inputClass"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : ariaInvalid"
            :required="required"
            :disabled="disabled"
            :default-value="defaultValue"
            v-bind="$attrs"
          />
        </InputGroup>
      </ComboboxAnchor>

      <ComboboxList class="w-(--reka-combobox-trigger-width)" align="start">
        <ComboboxInput
          v-model="searchTerm"
          :display-value="() => ''"
          :placeholder="searchPlaceholder"
          :aria-label="`${label} country code`"
        />

        <!-- Reka's ComboboxEmpty counts mounted items, which a virtualized list
        keeps at zero until rows are measured, so it would flash on open. -->
        <div
          v-if="filteredCodes.length === 0"
          class="py-6 text-center text-sm"
          data-slot="combobox-empty"
        >
          {{ emptyText }}
        </div>

        <ComboboxViewport class="max-h-72 p-1">
          <ComboboxVirtualizer
            v-slot="{ option }"
            :options="filteredCodes"
            :estimate-size="itemHeight"
            :text-content="(code) => countriesByCode.get(code)?.name ?? ''"
          >
            <ComboboxItem
              :value="option"
              :style="{ height: `${itemHeight}px` }"
              class="w-full"
            >
              <span aria-hidden="true">
                {{ countriesByCode.get(option)?.flag }}
              </span>
              <span class="flex-1 truncate">
                {{ countriesByCode.get(option)?.name }}
              </span>
              <span class="text-muted-foreground tabular-nums">
                {{ countriesByCode.get(option)?.dialCode }}
              </span>
              <ComboboxItemIndicator>
                <Check />
              </ComboboxItemIndicator>
            </ComboboxItem>
          </ComboboxVirtualizer>
        </ComboboxViewport>
      </ComboboxList>
    </Combobox>

    <input
      type="hidden"
      :name="submittedDialCodeName"
      :value="selectedDialCode"
      :disabled="disabled"
    />

    <FieldFeedback
      :description="description"
      :description-id="descriptionId"
      :error="error"
      :error-id="errorId"
    />
  </div>
</template>
