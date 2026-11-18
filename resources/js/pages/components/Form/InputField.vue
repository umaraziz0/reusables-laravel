<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import { formInputField } from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import InputField from '@/components/form/InputField.vue';
import Heading from '@/components/Heading.vue';
import {
  FieldDescription,
  FieldGroup,
  FieldLegend,
  FieldSet,
} from '@/components/ui/field';
import ComponentsLayout from '@/layouts/components/ComponentsLayout.vue';

// --- Props --- //

defineOptions({
  layout: [
    ComponentsLayout,
    {
      breadcrumbs: [
        {
          title: 'Form',
          href: formInputField(),
        },
        {
          title: 'Input Field',
          href: formInputField(),
        },
      ],
    },
  ],
});

const value = ref('');

const email = ref('');
</script>

<template>
  <Head title="Input Field" />

  <Heading
    title="Input Field"
    description="A labelled input with description and error feedback"
  />

  <div
    class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start 2xl:grid-cols-3"
  >
    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Default</h3>

      <InputField
        v-model="value"
        label="Name"
        name="name"
        placeholder="Input name..."
        autocomplete="name"
        :description="`Current value: ${value}`"
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Description</h3>

      <InputField
        label="API token"
        name="api_token"
        autocomplete="off"
        placeholder="sk_live_…"
        description="Create a token in the dashboard. It is shown only once."
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Error</h3>

      <InputField
        label="Username"
        name="username"
        default-value="ab"
        autocomplete="username"
        description="3–30 characters, letters and numbers only."
        error="Username must be at least 3 characters."
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Disabled</h3>
      <InputField
        label="Account ID"
        name="account_id"
        default-value="acct_01HZXD8QX9K2P4"
        disabled
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Read-only</h3>
      <InputField
        label="Created at"
        name="created_at"
        default-value="2026-04-10"
        readonly
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Hidden label</h3>
      <InputField
        label="Notes"
        name="notes"
        hide-label
        placeholder="Internal notes…"
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Custom width</h3>
      <InputField
        name="custom-width"
        placeholder="Custom width"
        class="max-w-72"
        label="Custom width"
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Required</h3>
      <InputField
        label="Full name"
        name="full_name"
        required
        placeholder="Jane Doe"
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">
        Email suggestion
      </h3>
      <InputField
        v-model="email"
        label="Email"
        name="email"
        type="email"
        autocomplete="email"
        placeholder="jane@example.com"
        description="Try jane@gmial.com, then move focus away."
      />
    </div>

    <div class="space-y-3 md:col-span-2 2xl:col-span-3">
      <h3 class="text-sm font-medium text-muted-foreground">Field set</h3>
      <FieldSet class="max-w-xl">
        <FieldLegend>Shipping address</FieldLegend>
        <FieldDescription>Where should we deliver your order?</FieldDescription>
        <FieldGroup>
          <InputField
            label="Street address"
            name="street_address"
            autocomplete="street-address"
            required
          />
          <div class="grid grid-cols-1 gap-7 sm:grid-cols-2">
            <InputField
              label="City"
              name="city"
              autocomplete="address-level2"
              required
            />
            <InputField
              label="Postal code"
              name="postal_code"
              autocomplete="postal-code"
              error="Enter a valid postal code."
            />
          </div>
        </FieldGroup>
      </FieldSet>
    </div>
  </div>
</template>
