<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ChevronRight, Mail } from '@lucide/vue';

import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { buttons } from '@/routes/components';

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Buttons',
        href: buttons().url,
      },
    ],
  },
});

const variants = [
  'default',
  'destructive',
  'outline',
  'secondary',
  'ghost',
  'link',
] as const;

const sizes = ['default', 'sm', 'lg', 'icon'] as const;
</script>

<template>
  <Head title="Buttons" />

  <Heading
    title="Button Variants"
    description="All available button variants and sizes"
  />

  <section class="space-y-4">
    <h3 class="text-sm font-medium text-muted-foreground">Variants</h3>
    <div class="flex flex-wrap gap-4">
      <Button v-for="variant in variants" :key="variant" :variant="variant">
        {{ variant }}
      </Button>
    </div>
  </section>

  <section class="space-y-4">
    <h3 class="text-sm font-medium text-muted-foreground">Sizes</h3>
    <div class="flex flex-wrap items-center gap-4">
      <Button v-for="size in sizes" :key="size" :size="size">
        <Mail v-if="size === 'icon'" />
        <template v-else>{{ size }}</template>
      </Button>
    </div>
  </section>

  <section class="space-y-4">
    <h3 class="text-sm font-medium text-muted-foreground">With icon</h3>
    <div class="flex flex-wrap gap-4">
      <Button>
        <Mail class="size-4" />
        Login with Email
      </Button>
      <Button variant="secondary">
        Next
        <ChevronRight class="size-4" />
      </Button>
    </div>
  </section>

  <section class="space-y-4">
    <h3 class="text-sm font-medium text-muted-foreground">Disabled</h3>
    <div class="flex flex-wrap gap-4">
      <Button disabled>Default</Button>
      <Button variant="destructive" disabled>Destructive</Button>
      <Button variant="outline" disabled>Outline</Button>
    </div>
  </section>
</template>
