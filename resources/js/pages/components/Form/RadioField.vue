<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import {
  formInputField,
  formRadioField,
} from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import RadioField, {
  type RadioFieldOption,
} from '@/components/form/RadioField.vue';
import Heading from '@/components/Heading.vue';

// --- Props --- //

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Form',
        href: formInputField(),
      },
      {
        title: 'Radio Field',
        href: formRadioField(),
      },
    ],
  },
});

const planOptions: RadioFieldOption[] = [
  { value: 'free', label: 'Free' },
  { value: 'pro', label: 'Pro' },
  { value: 'enterprise', label: 'Enterprise', disabled: true },
];

const billingCycleOptions: RadioFieldOption[] = [
  { value: 'monthly', label: 'Monthly' },
  { value: 'yearly', label: 'Yearly' },
];

const deliveryOptions: RadioFieldOption[] = [
  { value: 'standard', label: 'Standard', description: '3–5 business days' },
  { value: 'express', label: 'Express', description: 'Next business day' },
];

const sizeOptions: RadioFieldOption[] = [
  { value: 's', label: 'S' },
  { value: 'm', label: 'M' },
  { value: 'l', label: 'L' },
];

const regionOptions: RadioFieldOption[] = [
  { value: 'eu', label: 'Europe' },
  { value: 'us', label: 'United States' },
];

const sortOrderOptions: RadioFieldOption[] = [
  { value: 'asc', label: 'Ascending' },
  { value: 'desc', label: 'Descending' },
];

const selectedPlan = ref<string | number>();
</script>

<template>
  <Head title="Radio Field" />

  <Heading
    title="Radio Field"
    description="A labelled radio group with description and error feedback that submits the chosen value"
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

      <RadioField
        v-model="selectedPlan"
        label="Plan"
        name="plan"
        :options="planOptions"
        :description="`Current value: ${selectedPlan ?? 'none'}`"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Description
      </h4>

      <RadioField
        label="Billing cycle"
        name="billing_cycle"
        description="You can change this later."
        default-value="monthly"
        :options="billingCycleOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Option descriptions
      </h4>

      <RadioField
        label="Delivery"
        name="delivery"
        default-value="standard"
        :options="deliveryOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Horizontal
      </h4>

      <RadioField
        label="Size"
        name="size"
        orientation="horizontal"
        default-value="m"
        :options="sizeOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Error
      </h4>

      <RadioField
        label="Billing plan"
        name="billing_plan"
        required
        description="You can change this later."
        error="The billing plan field is required."
        :options="planOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Disabled
      </h4>

      <RadioField
        label="Region"
        name="region"
        description="Your admin controls this setting."
        default-value="eu"
        disabled
        :options="regionOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Hidden label
      </h4>

      <RadioField
        label="Sort order"
        name="sort_order"
        hide-label
        orientation="horizontal"
        default-value="asc"
        :options="sortOrderOptions"
      />
    </div>
  </div>
</template>
