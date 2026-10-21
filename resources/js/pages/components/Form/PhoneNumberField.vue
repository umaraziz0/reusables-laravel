<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import {
  formInputField,
  formPhoneNumberField,
} from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Components --- //
import PhoneNumberField from '@/components/form/PhoneNumberField.vue';
import Heading from '@/components/Heading.vue';
import { COUNTRIES } from '@/lib/countries';

// --- Props --- //

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Form',
        href: formInputField(),
      },
      {
        title: 'Phone Number Field',
        href: formPhoneNumberField(),
      },
    ],
  },
});

// --- Constants --- //

const ASEAN_COUNTRY_CODES = new Set([
  'BN',
  'KH',
  'ID',
  'LA',
  'MY',
  'MM',
  'PH',
  'SG',
  'TH',
  'VN',
]);

const aseanCountries = COUNTRIES.filter((country) =>
  ASEAN_COUNTRY_CODES.has(country.code),
);

// --- Refs --- //

const phone = ref('');

const dialCode = ref('+62');

const usPhone = ref('');

const usDialCode = ref('+1');

// --- Computed -- //

const e164 = computed(() => `${dialCode.value}${phone.value}`);
</script>

<template>
  <Head title="Phone Number Field" />

  <Heading
    title="Phone Number Field"
    description="A searchable country code picker combined with a phone number input"
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

      <PhoneNumberField
        v-model="phone"
        v-model:dial-code="dialCode"
        label="Phone number"
        name="phone"
        placeholder="812 3456 7890"
        :description="`Current value: ${e164}`"
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Shared dial code
      </h4>

      <PhoneNumberField
        v-model="usPhone"
        v-model:dial-code="usDialCode"
        label="US or Canada phone"
        name="us_phone"
        placeholder="555 123 4567"
        description="+1 shows the United States until another +1 country is picked."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Restricted countries
      </h4>

      <PhoneNumberField
        label="ASEAN phone"
        name="asean_phone"
        :countries="aseanCountries"
        description="Only ASEAN member states are listed."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Error
      </h4>

      <PhoneNumberField
        label="Mobile number"
        name="mobile"
        default-value="12"
        description="We text the verification code here."
        error="Enter a valid phone number."
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Disabled
      </h4>

      <PhoneNumberField
        label="Office number"
        name="office_phone"
        default-dial-code="+65"
        default-value="6123 4567"
        disabled
      />
    </div>

    <div class="space-y-3">
      <h4
        class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
      >
        Required
      </h4>

      <PhoneNumberField
        label="Emergency contact"
        name="emergency_phone"
        required
      />
    </div>
  </div>
</template>
