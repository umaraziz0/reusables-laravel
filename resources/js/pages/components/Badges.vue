<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { badges } from '@/routes/components';

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Badges',
        href: badges().url,
      },
    ],
  },
});

const variants = ['default', 'secondary', 'destructive', 'outline'] as const;
</script>

<template>
  <Head title="Badges" />

  <Heading title="Badge Variants" description="All available badge variants" />

  <section class="space-y-4">
    <h3 class="text-sm font-medium text-muted-foreground">Variants</h3>
    <div class="flex flex-wrap gap-4">
      <Badge v-for="variant in variants" :key="variant" :variant="variant">
        {{ variant }}
      </Badge>
    </div>
  </section>

  <section class="space-y-4">
    <h3 class="text-sm font-medium text-muted-foreground">Usage examples</h3>
    <div class="flex flex-wrap items-center gap-4">
      <Badge>New</Badge>
      <Badge variant="secondary">Draft</Badge>
      <Badge variant="destructive">Deleted</Badge>
      <Badge variant="outline">Archived</Badge>
    </div>
  </section>
</template>
