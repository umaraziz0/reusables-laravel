<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { AtSign, Check, Copy, Search } from '@lucide/vue';
import { computed, ref } from 'vue';

import { formInputGroupField } from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import InputGroupField from '@/components/form/InputGroupField.vue';
import Heading from '@/components/Heading.vue';
import { InputGroupButton, InputGroupText } from '@/components/ui/input-group';

// --- Props --- //

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Form',
        href: formInputGroupField(),
      },
      {
        title: 'Input Group Field',
        href: formInputGroupField(),
      },
    ],
  },
});

// --- Constants --- //

const BIO_MAX_LENGTH = 80;

const API_KEY = 'sk_test_example_key';

// --- Refs --- //

const search = ref('');

const bio = ref('');

const isApiKeyCopied = ref(false);

// --- Computed -- //

const bioLength = computed(() => bio.value.length);

// --- Methods --- //

async function copyApiKey(): Promise<void> {
  await navigator.clipboard.writeText(API_KEY);
  isApiKeyCopied.value = true;
}
</script>

<template>
  <Head title="Input Group Field" />

  <Heading
    title="Input Group Field"
    description="A labelled input with addons, description and error feedback"
  />

  <div
    class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start 2xl:grid-cols-3"
  >
    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Leading icon
      </h4>

      <InputGroupField
        v-model="search"
        label="Search"
        name="search"
        type="search"
        placeholder="Search…"
        :description="`Current value: ${search}`"
      >
        <template #start>
          <Search />
        </template>
      </InputGroupField>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Leading and trailing text
      </h4>

      <InputGroupField
        label="Website"
        name="website"
        placeholder="example"
        autocomplete="off"
      >
        <template #start>
          <InputGroupText>https://</InputGroupText>
        </template>
        <template #end>
          <InputGroupText>.com</InputGroupText>
        </template>
      </InputGroupField>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Trailing button
      </h4>

      <InputGroupField
        label="API key"
        name="api_key"
        :default-value="API_KEY"
        readonly
      >
        <template #end>
          <InputGroupButton
            size="icon-xs"
            :aria-label="isApiKeyCopied ? 'Copied' : 'Copy API key'"
            @click="copyApiKey"
          >
            <Check v-if="isApiKeyCopied" />
            <Copy v-else />
          </InputGroupButton>
        </template>
      </InputGroupField>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Keyboard hint
      </h4>

      <InputGroupField
        label="Command"
        name="command"
        hide-label
        placeholder="Type a command…"
      >
        <template #end>
          <kbd
            class="pointer-events-none rounded border bg-muted px-1.5 font-mono text-xs"
          >
            ⌘K
          </kbd>
        </template>
      </InputGroupField>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Block end
      </h4>

      <InputGroupField
        v-model="bio"
        label="Short bio"
        name="bio"
        :maxlength="BIO_MAX_LENGTH"
        placeholder="Tell us about yourself"
      >
        <template #block-end>
          <InputGroupText class="ml-auto">
            {{ bioLength }}/{{ BIO_MAX_LENGTH }}
          </InputGroupText>
        </template>
      </InputGroupField>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Error
      </h4>

      <InputGroupField
        label="Username"
        name="username"
        default-value="ab"
        autocomplete="username"
        description="3–30 characters, letters and numbers only."
        error="Username must be at least 3 characters."
      >
        <template #start>
          <AtSign />
        </template>
      </InputGroupField>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Disabled
      </h4>

      <InputGroupField
        label="Account ID"
        name="account_id"
        default-value="acct_01HZXD8QX9K2P4"
        disabled
      >
        <template #start>
          <InputGroupText>ID</InputGroupText>
        </template>
      </InputGroupField>
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Required
      </h4>

      <InputGroupField
        label="Email"
        name="email"
        type="email"
        required
        autocomplete="email"
        placeholder="jane@example.com"
        description="We never share it."
      >
        <template #start>
          <AtSign />
        </template>
      </InputGroupField>
    </div>
  </div>
</template>
