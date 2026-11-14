<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import {
  formInputField,
  formTextareaField,
} from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import InputField from '@/components/form/InputField.vue';
import TextareaField from '@/components/form/TextareaField.vue';
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
        title: 'Textarea Field',
        href: formTextareaField(),
      },
    ],
  },
});

const value = ref('');

const status = ref('');
</script>

<template>
  <Head title="Textarea Field" />

  <Heading
    title="Textarea Field"
    description="A labelled multi-line input with description, error feedback and an optional character counter"
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

      <TextareaField
        v-model="value"
        label="Message"
        name="message"
        placeholder="Type your message…"
        :description="`Current value: ${value}`"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Description
      </h4>

      <TextareaField
        label="Bio"
        name="bio"
        placeholder="Tell us a little about yourself"
        description="Shown on your public profile."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Error
      </h4>

      <TextareaField
        label="Reason for refund"
        name="refund_reason"
        default-value="Broken"
        description="At least 20 characters."
        error="Reason must be at least 20 characters."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Disabled
      </h4>
      <TextareaField
        label="Internal notes"
        name="internal_notes"
        default-value="Customer requested a callback on Monday."
        disabled
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Read-only
      </h4>
      <TextareaField
        label="Original request"
        name="original_request"
        default-value="Please update the billing address on my account."
        readonly
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Hidden label
      </h4>
      <TextareaField
        label="Comment"
        name="comment"
        hide-label
        placeholder="Add a comment…"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Custom width
      </h4>
      <TextareaField
        name="custom-width"
        placeholder="Custom width"
        class="max-w-72"
        label="Custom width"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Required
      </h4>
      <TextareaField
        label="Cover letter"
        name="cover_letter"
        required
        placeholder="Why do you want this role?"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Rows
      </h4>
      <TextareaField
        label="Release notes"
        name="release_notes"
        rows="6"
        textarea-class="field-sizing-fixed"
        description="Fixed at six rows; scrolls once content overflows."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Character counter
      </h4>
      <TextareaField
        v-model="status"
        label="Status update"
        name="status"
        :maxlength="280"
        placeholder="What's happening?"
        description="Keep it short."
      />
    </div>

    <div class="space-y-3 md:col-span-2 2xl:col-span-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Field set
      </h4>
      <FieldSet class="max-w-xl">
        <FieldLegend>Feedback</FieldLegend>
        <FieldDescription>Tell us how we're doing.</FieldDescription>
        <FieldGroup>
          <InputField label="Subject" name="feedback_subject" required />
          <TextareaField
            label="Details"
            name="feedback_details"
            required
            :maxlength="1000"
          />
        </FieldGroup>
      </FieldSet>
    </div>
  </div>
</template>
