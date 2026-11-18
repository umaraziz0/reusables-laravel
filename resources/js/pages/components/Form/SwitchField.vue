<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import {
  formInputField,
  formSwitchField,
} from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import SwitchField from '@/components/form/SwitchField.vue';
import Heading from '@/components/Heading.vue';
import {
  FieldDescription,
  FieldGroup,
  FieldLegend,
  FieldSeparator,
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
          title: 'Switch Field',
          href: formSwitchField(),
        },
      ],
    },
  ],
});

const isDarkMode = ref(false);
</script>

<template>
  <Head title="Switch Field" />

  <Heading
    title="Switch Field"
    description='A labelled on/off switch with description and error feedback that submits "1" when on'
  />

  <div
    class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start 2xl:grid-cols-3"
  >
    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Default</h3>

      <SwitchField
        v-model="isDarkMode"
        label="Dark mode"
        name="dark_mode"
        :description="`Current value: ${isDarkMode}`"
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Description</h3>

      <SwitchField
        label="Marketing emails"
        name="marketing_emails"
        description="Receive emails about new products and features."
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Default value</h3>

      <SwitchField
        label="Remember this device"
        name="remember_device"
        :default-value="true"
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Error</h3>

      <SwitchField
        label="Public profile"
        name="public_profile"
        description="Anyone can see your profile."
        error="Verify your email before making your profile public."
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Required</h3>

      <SwitchField
        label="Share usage data"
        name="share_usage"
        description="Required while you are in the beta."
        required
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Disabled</h3>

      <SwitchField
        label="Two-factor login"
        name="two_factor"
        description="Your admin controls this setting."
        :default-value="true"
        disabled
      />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Hidden label</h3>

      <SwitchField label="Enable row" name="enable_row" hide-label />
    </div>

    <div class="space-y-3">
      <h3 class="text-sm font-medium text-muted-foreground">Custom value</h3>

      <SwitchField
        label="Opt in to beta features"
        name="beta"
        value="yes"
        description='Submits "yes" when on.'
      />
    </div>

    <div class="space-y-3 md:col-span-2 2xl:col-span-3">
      <h3 class="text-sm font-medium text-muted-foreground">Settings list</h3>
      <FieldSet class="max-w-xl">
        <FieldLegend>Notifications</FieldLegend>
        <FieldDescription>Choose what we email you about.</FieldDescription>
        <FieldGroup>
          <SwitchField
            label="Comments on my posts"
            name="notify_comments"
            description="When someone replies to something you wrote."
          />
          <FieldSeparator />
          <SwitchField
            label="New followers"
            name="notify_followers"
            description="When someone starts following you."
          />
          <FieldSeparator />
          <SwitchField
            label="Security alerts"
            name="notify_security"
            description="Always on."
            :default-value="true"
            disabled
          />
        </FieldGroup>
      </FieldSet>
    </div>
  </div>
</template>
