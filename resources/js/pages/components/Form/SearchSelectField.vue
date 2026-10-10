<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import {
  formInputField,
  formSearchSelectField,
} from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import SearchSelectField from '@/components/form/SearchSelectField.vue';
import SelectField, {
  type SelectFieldOption,
} from '@/components/form/SelectField.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';

// --- Props --- //

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Form',
        href: formInputField(),
      },
      {
        title: 'Search Select Field',
        href: formSearchSelectField(),
      },
    ],
  },
});

// --- Constants --- //

const countryOptions = [
  'Australia',
  'Austria',
  'Brazil',
  'Canada',
  'Denmark',
  'Egypt',
  'France',
  'Germany',
  'India',
  'Japan',
  'Kenya',
  'Malaysia',
  'Netherlands',
  'Norway',
  'Pakistan',
  'Singapore',
  'Spain',
  'Sweden',
  'United Kingdom',
  'United States',
].map((country) => ({ value: country, label: country }));

const tenThousandOptions = generateOptions(10_000);

const hundredThousandOptions = generateOptions(100_000);

// --- Refs --- //

const country = ref<string | number>();

const largeListItem = ref<string | number>(4_200);

const isComparisonMounted = ref(false);

// --- Methods --- //

function generateOptions(count: number): SelectFieldOption[] {
  return Array.from({ length: count }, (_, index) => ({
    value: index,
    label: `Item ${String(index + 1).padStart(6, '0')}`,
    disabled: index % 50 === 7,
  }));
}
</script>

<template>
  <Head title="Search Select Field" />

  <Heading
    title="Search Select Field"
    description="A searchable select that only renders the visible options, for lists with thousands of items"
  />

  <div
    class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start 2xl:grid-cols-3"
  >
    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Default
      </h4>

      <SearchSelectField
        v-model="country"
        label="Country"
        name="country"
        placeholder="Choose a country…"
        search-placeholder="Search countries…"
        empty-text="No countries found."
        :options="countryOptions"
        :description="`Current value: ${country ?? ''}`"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        10,000 options
      </h4>

      <SearchSelectField
        v-model="largeListItem"
        label="Item"
        name="item"
        placeholder="Choose an item…"
        :options="tenThousandOptions"
        description="Opens scrolled to the selected item. Every 50th item is disabled."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        100,000 options
      </h4>

      <SearchSelectField
        label="Large item"
        name="large_item"
        placeholder="Choose an item…"
        :options="hundredThousandOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Error
      </h4>

      <SearchSelectField
        label="Billing country"
        name="billing_country"
        placeholder="Choose a country…"
        :options="countryOptions"
        description="Where you pay tax."
        error="Choose a country to continue."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Disabled
      </h4>

      <SearchSelectField
        label="Region"
        name="region"
        default-value="eu-west-1"
        :options="[{ value: 'eu-west-1', label: 'EU (Ireland)' }]"
        disabled
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Required
      </h4>

      <SearchSelectField
        label="Shipping country"
        name="shipping_country"
        required
        placeholder="Choose a country…"
        :options="countryOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Hidden label
      </h4>

      <SearchSelectField
        label="Filter by country"
        name="filter_country"
        hide-label
        placeholder="Filter by country…"
        :options="countryOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Custom width
      </h4>

      <SearchSelectField
        label="Custom width"
        name="custom_width"
        placeholder="Custom width"
        trigger-class="w-48"
        :options="countryOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Comparison: Select Field, 10,000 options
      </h4>

      <!-- Reka's Select mounts every item even while closed, which blocks the
      page for seconds, so this only mounts on request. -->
      <SelectField
        v-if="isComparisonMounted"
        label="Item (not virtualized)"
        name="item_unvirtualized"
        placeholder="Choose an item…"
        :options="tenThousandOptions"
        description="Mounts every option. Compare how long it took to appear."
      />
      <Button v-else variant="outline" @click="isComparisonMounted = true">
        Mount 10,000-option Select Field
      </Button>
    </div>
  </div>
</template>
