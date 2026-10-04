<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { AlertCircle, Info } from '@lucide/vue';

import Heading from '@/components/Heading.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { alert } from '@/routes/components';

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Alert',
        href: alert().url,
      },
    ],
  },
});
</script>

<template>
  <Head title="Alert" />

  <Heading title="Alert" description="Display a callout for user attention" />

  <section class="space-y-4">
    <h3 class="text-sm font-medium text-muted-foreground">Variants</h3>
    <div class="flex flex-col gap-4">
      <Alert>
        <AlertTitle>Default alert</AlertTitle>
        <AlertDescription>
          This is the default alert with a title and description. Use it for
          informational content.
        </AlertDescription>
      </Alert>
      <Alert variant="destructive">
        <AlertTitle>Destructive alert</AlertTitle>
        <AlertDescription>
          This alert indicates an error or destructive action. Use for
          validation errors or critical messages.
        </AlertDescription>
      </Alert>
    </div>
  </section>

  <section class="space-y-4">
    <h3 class="text-sm font-medium text-muted-foreground">With icon</h3>
    <div class="flex flex-col gap-4">
      <Alert>
        <Info class="size-4" />
        <AlertTitle>Information</AlertTitle>
        <AlertDescription>
          You can add a leading icon to provide visual context for the alert.
        </AlertDescription>
      </Alert>
      <Alert variant="destructive">
        <AlertCircle class="size-4" />
        <AlertTitle>Error</AlertTitle>
        <AlertDescription>
          Something went wrong. Please try again or contact support.
        </AlertDescription>
      </Alert>
    </div>
  </section>
</template>
