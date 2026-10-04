<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import { formBasicInput } from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import BasicInput from '@/components/form/BasicInput.vue';
import Heading from '@/components/Heading.vue';

// --- Props --- //

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Form',
        href: formBasicInput(),
      },
      {
        title: 'Basic Input',
        href: formBasicInput(),
      },
    ],
  },
});

const value = ref('');
</script>

<template>
  <Head title="Basic Input" />

  <Heading title="Basic Input" description="A basic input component" />

  <div
    class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start 2xl:grid-cols-3"
  >
    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Default
      </h4>

      <BasicInput
        v-model="value"
        label="Name"
        name="name"
        placeholder="Input name..."
        autocomplete="name"
      >
        <template #description>
          <span>Current value: {{ value }}</span>
        </template>
      </BasicInput>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Description
      </h4>

      <BasicInput
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

      <BasicInput
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
      <BasicInput
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
      <BasicInput
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
        Custom label slot
      </h4>
      <BasicInput v-model="value" name="notes" placeholder="Internal notes…">
        <template #label>
          <span>Notes</span>
          <span class="font-normal text-muted-foreground">(optional)</span>
        </template>
      </BasicInput>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Hidden label
      </h4>
      <BasicInput
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
      <BasicInput
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
      <BasicInput
        label="Full name"
        name="full_name"
        required
        placeholder="Jane Doe"
      />
    </div>
  </div>
</template>
