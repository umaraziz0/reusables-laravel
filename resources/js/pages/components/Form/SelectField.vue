<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import {
  formInputField,
  formSelectField,
} from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import SelectField, {
  type SelectFieldOption,
} from '@/components/form/SelectField.vue';
import Heading from '@/components/Heading.vue';
import {
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectSeparator,
} from '@/components/ui/select';

// --- Props --- //

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Form',
        href: formInputField(),
      },
      {
        title: 'Select Field',
        href: formSelectField(),
      },
    ],
  },
});

// --- Constants --- //

const planOptions = [
  { value: 'free', label: 'Free' },
  { value: 'pro', label: 'Pro' },
  { value: 'team', label: 'Team' },
] satisfies SelectFieldOption[];

const roleOptions = [
  { value: 'viewer', label: 'Viewer' },
  { value: 'editor', label: 'Editor' },
  { value: 'owner', label: 'Owner', disabled: true },
] satisfies SelectFieldOption[];

const countryOptions = [
  'Australia',
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

// --- Refs --- //

const plan = ref<string | number>();
</script>

<template>
  <Head title="Select Field" />

  <Heading
    title="Select Field"
    description="A labelled select with description and error feedback"
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

      <SelectField
        v-model="plan"
        label="Plan"
        name="plan"
        placeholder="Choose a plan…"
        :options="planOptions"
        :description="`Current value: ${plan ?? ''}`"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Description
      </h4>

      <SelectField
        label="Billing cycle"
        name="billing_cycle"
        placeholder="Choose a cycle…"
        :options="[
          { value: 'monthly', label: 'Monthly' },
          { value: 'yearly', label: 'Yearly' },
        ]"
        description="Yearly billing saves two months."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Error
      </h4>

      <SelectField
        label="Team plan"
        name="team_plan"
        placeholder="Choose a plan…"
        :options="planOptions"
        description="Pick the plan for this team."
        error="Choose a plan to continue."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Disabled
      </h4>
      <SelectField
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
        Disabled option
      </h4>
      <SelectField
        label="Role"
        name="role"
        placeholder="Choose a role…"
        :options="roleOptions"
        description="Only an owner can grant ownership."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Hidden label
      </h4>
      <SelectField
        label="Sort by"
        name="sort"
        hide-label
        placeholder="Sort by…"
        :options="[
          { value: 'newest', label: 'Newest' },
          { value: 'oldest', label: 'Oldest' },
        ]"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Custom width
      </h4>
      <SelectField
        label="Custom width"
        name="custom-width"
        placeholder="Custom width"
        trigger-class="w-48"
        :options="planOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Required
      </h4>
      <SelectField
        label="Account plan"
        name="account_plan"
        required
        placeholder="Choose a plan…"
        :options="planOptions"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Grouped
      </h4>
      <SelectField label="Timezone" name="timezone" placeholder="Timezone…">
        <SelectGroup>
          <SelectLabel>Europe</SelectLabel>
          <SelectItem value="Europe/London">London</SelectItem>
          <SelectItem value="Europe/Berlin">Berlin</SelectItem>
        </SelectGroup>
        <SelectSeparator />
        <SelectGroup>
          <SelectLabel>Asia</SelectLabel>
          <SelectItem value="Asia/Karachi">Karachi</SelectItem>
          <SelectItem value="Asia/Tokyo">Tokyo</SelectItem>
        </SelectGroup>
      </SelectField>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Long list
      </h4>
      <SelectField
        label="Country"
        name="country"
        placeholder="Choose a country…"
        :options="countryOptions"
      />
    </div>
  </div>
</template>
