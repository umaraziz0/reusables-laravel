<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import { formInputField } from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import InputField from '@/components/form/InputField.vue';
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
        title: 'Input Field',
        href: formInputField(),
      },
    ],
  },
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
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Default
      </h4>

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
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Description
      </h4>

      <InputField
        label="API token"
        name="api_token"
        autocomplete="off"
        placeholder="sk_live_…"
        description="Create a token in the dashboard. It is shown only once."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Error
      </h4>

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
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Disabled
      </h4>
      <InputField
        label="Account ID"
        name="account_id"
        default-value="acct_01HZXD8QX9K2P4"
        disabled
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Read-only
      </h4>
      <InputField
        label="Created at"
        name="created_at"
        default-value="2026-04-10"
        readonly
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Hidden label
      </h4>
      <InputField
        label="Notes"
        name="notes"
        hide-label
        placeholder="Internal notes…"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Custom width
      </h4>
      <InputField
        name="custom-width"
        placeholder="Custom width"
        input-class="w-72"
        label="Custom width"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Required
      </h4>
      <InputField
        label="Full name"
        name="full_name"
        required
        placeholder="Jane Doe"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Email suggestion
      </h4>
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
  </div>
</template>
