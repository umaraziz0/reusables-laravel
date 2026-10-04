<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ChevronDown } from '@lucide/vue';

import Heading from '@/components/Heading.vue';
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { collapsible } from '@/routes/components';

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Collapsible',
        href: collapsible().url,
      },
    ],
  },
});
</script>

<template>
  <Head title="Collapsible" />

  <div class="space-y-12 px-4 py-6">
    <Heading
      title="Collapsible"
      description="An expandable section that shows or hides content"
    />

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">
        Default (closed)
      </h3>
      <Collapsible
        class="w-full max-w-md rounded-lg border border-border bg-card"
      >
        <CollapsibleTrigger>
          <span>What is a collapsible?</span>
          <ChevronDown class="size-4 shrink-0 transition-transform" />
        </CollapsibleTrigger>
        <CollapsibleContent>
          <div
            class="border-t border-border px-4 py-3 text-sm text-muted-foreground"
          >
            A collapsible lets you show or hide content with a single click. It
            provides an accessible way to expand and collapse sections
            dynamically.
          </div>
        </CollapsibleContent>
      </Collapsible>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">Default open</h3>
      <Collapsible
        default-open
        class="w-full max-w-md rounded-lg border border-border bg-card"
      >
        <CollapsibleTrigger>
          <span>Click to collapse</span>
          <ChevronDown class="size-4 shrink-0 transition-transform" />
        </CollapsibleTrigger>
        <CollapsibleContent>
          <div
            class="border-t border-border px-4 py-3 text-sm text-muted-foreground"
          >
            This content is visible by default. Click the trigger to hide it.
          </div>
        </CollapsibleContent>
      </Collapsible>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">
        Multiple items (FAQ style)
      </h3>
      <div class="w-full max-w-md space-y-2">
        <Collapsible
          v-for="(item, i) in [
            {
              question: 'How do I get started?',
              answer:
                'Begin by creating an account and completing your profile. Then explore the dashboard to see what options are available.',
            },
            {
              question: 'Can I cancel anytime?',
              answer:
                'Yes, you can cancel your subscription at any time. Your access will continue until the end of your billing period.',
            },
            {
              question: 'Is there a free trial?',
              answer:
                'We offer a 14-day free trial for new users. No credit card required to sign up.',
            },
          ]"
          :key="i"
          class="rounded-lg border border-border bg-card"
        >
          <CollapsibleTrigger>
            <span>{{ item.question }}</span>
            <ChevronDown class="size-4 shrink-0 transition-transform" />
          </CollapsibleTrigger>
          <CollapsibleContent>
            <div
              class="border-t border-border px-4 py-3 text-sm text-muted-foreground"
            >
              {{ item.answer }}
            </div>
          </CollapsibleContent>
        </Collapsible>
      </div>
    </section>
  </div>
</template>
