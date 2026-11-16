<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import Heading from '@/components/Heading.vue';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationItem,
  PaginationLast,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';
import { pagination } from '@/routes/components';

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Pagination',
        href: pagination().url,
      },
    ],
  },
});

// --- Constants --- //

const itemsPerPage = 10;

const controlledTotal = 120;

// --- State --- //

const controlledPage = ref(3);
</script>

<template>
  <Head title="Pagination" />

  <div class="space-y-12 px-4 py-6">
    <Heading
      title="Pagination"
      description="Navigation between pages of results"
    />

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">Default</h3>
      <Pagination
        v-slot="{ page }"
        :total="100"
        :items-per-page="itemsPerPage"
        :default-page="2"
      >
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious />
          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              v-if="item.type === 'page'"
              :value="item.value"
              :is-active="item.value === page"
            >
              {{ item.value }}
            </PaginationItem>
            <PaginationEllipsis v-else :index="index" />
          </template>
          <PaginationNext />
        </PaginationContent>
      </Pagination>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">
        With first and last
      </h3>
      <Pagination
        v-slot="{ page }"
        :total="200"
        :items-per-page="itemsPerPage"
        :default-page="10"
      >
        <PaginationContent v-slot="{ items }" class="flex-wrap justify-center">
          <PaginationFirst />
          <PaginationPrevious />
          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              v-if="item.type === 'page'"
              :value="item.value"
              :is-active="item.value === page"
            >
              {{ item.value }}
            </PaginationItem>
            <PaginationEllipsis v-else :index="index" />
          </template>
          <PaginationNext />
          <PaginationLast />
        </PaginationContent>
      </Pagination>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">
        Show edges with more siblings
      </h3>
      <Pagination
        v-slot="{ page }"
        :total="500"
        :items-per-page="itemsPerPage"
        :default-page="25"
        :sibling-count="2"
        show-edges
      >
        <PaginationContent v-slot="{ items }" class="flex-wrap justify-center">
          <PaginationPrevious />
          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              v-if="item.type === 'page'"
              :value="item.value"
              :is-active="item.value === page"
            >
              {{ item.value }}
            </PaginationItem>
            <PaginationEllipsis v-else :index="index" />
          </template>
          <PaginationNext />
        </PaginationContent>
      </Pagination>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">Few pages</h3>
      <Pagination v-slot="{ page }" :total="30" :items-per-page="itemsPerPage">
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious />
          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              v-if="item.type === 'page'"
              :value="item.value"
              :is-active="item.value === page"
            >
              {{ item.value }}
            </PaginationItem>
          </template>
          <PaginationNext />
        </PaginationContent>
      </Pagination>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">Controlled</h3>
      <Pagination
        v-slot="{ page }"
        v-model:page="controlledPage"
        :total="controlledTotal"
        :items-per-page="itemsPerPage"
      >
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious />
          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              v-if="item.type === 'page'"
              :value="item.value"
              :is-active="item.value === page"
            >
              {{ item.value }}
            </PaginationItem>
            <PaginationEllipsis v-else :index="index" />
          </template>
          <PaginationNext />
        </PaginationContent>
      </Pagination>
      <p class="text-center text-sm text-muted-foreground">
        Page {{ controlledPage }} of
        {{ Math.ceil(controlledTotal / itemsPerPage) }}
      </p>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">Disabled</h3>
      <Pagination
        v-slot="{ page }"
        :total="100"
        :items-per-page="itemsPerPage"
        disabled
      >
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious />
          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              v-if="item.type === 'page'"
              :value="item.value"
              :is-active="item.value === page"
            >
              {{ item.value }}
            </PaginationItem>
            <PaginationEllipsis v-else :index="index" />
          </template>
          <PaginationNext />
        </PaginationContent>
      </Pagination>
    </section>
  </div>
</template>
