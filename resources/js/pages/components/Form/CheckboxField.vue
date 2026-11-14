<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import {
  formCheckboxField,
  formInputField,
} from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import CheckboxField from '@/components/form/CheckboxField.vue';
import Heading from '@/components/Heading.vue';
import {
  FieldDescription,
  FieldGroup,
  FieldLegend,
  FieldSet,
} from '@/components/ui/field';

// --- Props --- //

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Form',
        href: formInputField(),
      },
      {
        title: 'Checkbox Field',
        href: formCheckboxField(),
      },
    ],
  },
});

const isSubscribed = ref(false);
</script>

<template>
  <Head title="Checkbox Field" />

  <Heading
    title="Checkbox Field"
    description='A labelled checkbox with description and error feedback that submits "1" when checked'
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

      <CheckboxField
        v-model="isSubscribed"
        label="Email me about new releases"
        name="newsletter"
        :description="`Current value: ${isSubscribed}`"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Description
      </h4>

      <CheckboxField
        label="Product updates"
        name="product_updates"
        description="At most one email per month."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Default value
      </h4>

      <CheckboxField
        label="Remember this device"
        name="remember_device"
        :default-value="true"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Error
      </h4>

      <CheckboxField
        label="I accept the terms"
        name="terms"
        required
        description="Read the terms before you continue."
        error="You must accept the terms."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Required
      </h4>

      <CheckboxField
        label="I confirm the details are correct"
        name="confirm_details"
        required
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Disabled
      </h4>

      <CheckboxField
        label="Two-factor login"
        name="two_factor"
        description="Your admin controls this setting."
        :default-value="true"
        disabled
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Hidden label
      </h4>

      <CheckboxField label="Select row" name="select_row" hide-label />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Custom value
      </h4>

      <CheckboxField
        label="Opt in to beta features"
        name="beta"
        value="yes"
        description='Submits "yes" when checked.'
      />
    </div>

    <div class="space-y-3 md:col-span-2 2xl:col-span-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Field set
      </h4>
      <FieldSet class="max-w-xl">
        <FieldLegend>Notifications</FieldLegend>
        <FieldDescription>Choose what we email you about.</FieldDescription>
        <FieldGroup>
          <CheckboxField label="Comments on my posts" name="notify_comments" />
          <CheckboxField label="New followers" name="notify_followers" />
          <CheckboxField
            label="Security alerts"
            name="notify_security"
            :default-value="true"
            disabled
            description="Always on."
          />
        </FieldGroup>
      </FieldSet>
    </div>
  </div>
</template>
