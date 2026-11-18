<script setup lang="ts">
import type { LengthAwarePaginator } from '@/types/Pagination';

import { Head, router } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableEmpty,
  TableFooter,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import ComponentsLayout from '@/layouts/components/ComponentsLayout.vue';
import { table } from '@/routes/components';

type InvoiceStatus = 'paid' | 'pending' | 'unpaid';

type Invoice = {
  id: number;
  number: string;
  status: InvoiceStatus;
  method: string;
  amount: number;
};

defineOptions({
  layout: [
    ComponentsLayout,
    {
      breadcrumbs: [
        {
          title: 'Table',
          href: table().url,
        },
      ],
    },
  ],
});

const props = defineProps<{
  invoices: LengthAwarePaginator<Invoice>;
}>();

// --- Constants --- //

const sampleInvoices: Invoice[] = [
  {
    id: 1,
    number: 'INV-001',
    status: 'paid',
    method: 'Credit Card',
    amount: 250,
  },
  {
    id: 2,
    number: 'INV-002',
    status: 'pending',
    method: 'PayPal',
    amount: 150,
  },
  {
    id: 3,
    number: 'INV-003',
    status: 'unpaid',
    method: 'Bank Transfer',
    amount: 350,
  },
  {
    id: 4,
    number: 'INV-004',
    status: 'paid',
    method: 'Credit Card',
    amount: 450,
  },
  { id: 5, number: 'INV-005', status: 'paid', method: 'PayPal', amount: 550 },
];

const statusVariants: Record<
  InvoiceStatus,
  'default' | 'secondary' | 'destructive'
> = {
  paid: 'default',
  pending: 'secondary',
  unpaid: 'destructive',
};

const sampleTotal = sampleInvoices.reduce(
  (total, invoice) => total + invoice.amount,
  0,
);

// --- Methods --- //

function formatAmount(amount: number): string {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
}

function goToPage(page: number): void {
  router.get(
    table().url,
    { page },
    { only: ['invoices'], preserveState: true, preserveScroll: true },
  );
}
</script>

<template>
  <Head title="Table" />

  <div class="space-y-12 px-4 py-6">
    <Heading
      title="Table"
      description="A responsive table for displaying rows of data"
    />

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">Basic</h3>
      <Table>
        <TableCaption>A list of your recent invoices.</TableCaption>
        <TableHeader>
          <TableRow>
            <TableHead class="w-[100px]">Invoice</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Method</TableHead>
            <TableHead class="text-right">Amount</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="invoice in sampleInvoices" :key="invoice.id">
            <TableCell class="font-medium">{{ invoice.number }}</TableCell>
            <TableCell class="capitalize">{{ invoice.status }}</TableCell>
            <TableCell>{{ invoice.method }}</TableCell>
            <TableCell class="text-right">
              {{ formatAmount(invoice.amount) }}
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">
        With footer and badges
      </h3>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead class="w-[100px]">Invoice</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Method</TableHead>
            <TableHead class="text-right">Amount</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="invoice in sampleInvoices" :key="invoice.id">
            <TableCell class="font-medium">{{ invoice.number }}</TableCell>
            <TableCell>
              <Badge
                :variant="statusVariants[invoice.status]"
                class="capitalize"
              >
                {{ invoice.status }}
              </Badge>
            </TableCell>
            <TableCell>{{ invoice.method }}</TableCell>
            <TableCell class="text-right">
              {{ formatAmount(invoice.amount) }}
            </TableCell>
          </TableRow>
        </TableBody>
        <TableFooter>
          <TableRow>
            <TableCell :colspan="3">Total</TableCell>
            <TableCell class="text-right">
              {{ formatAmount(sampleTotal) }}
            </TableCell>
          </TableRow>
        </TableFooter>
      </Table>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">Empty</h3>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Invoice</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Method</TableHead>
            <TableHead class="text-right">Amount</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableEmpty :colspan="4">No results.</TableEmpty>
        </TableBody>
      </Table>
    </section>

    <section class="space-y-4">
      <h3 class="text-sm font-medium text-muted-foreground">
        Server-side pagination
      </h3>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead class="w-[100px]">Invoice</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Method</TableHead>
            <TableHead class="text-right">Amount</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="props.invoices.data.length > 0">
            <TableRow v-for="invoice in props.invoices.data" :key="invoice.id">
              <TableCell class="font-medium">{{ invoice.number }}</TableCell>
              <TableCell>
                <Badge
                  :variant="statusVariants[invoice.status]"
                  class="capitalize"
                >
                  {{ invoice.status }}
                </Badge>
              </TableCell>
              <TableCell>{{ invoice.method }}</TableCell>
              <TableCell class="text-right">
                {{ formatAmount(invoice.amount) }}
              </TableCell>
            </TableRow>
          </template>
          <TableEmpty v-else :colspan="4">No results.</TableEmpty>
        </TableBody>
      </Table>

      <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <p class="text-sm whitespace-nowrap text-muted-foreground">
          <template v-if="props.invoices.from">
            Showing {{ props.invoices.from }}–{{ props.invoices.to }} of
            {{ props.invoices.total }}
          </template>
          <template v-else>No invoices on this page</template>
        </p>
        <Pagination
          v-slot="{ page }"
          :page="props.invoices.current_page"
          :total="props.invoices.total"
          :items-per-page="props.invoices.per_page"
          class="mx-0 w-auto"
          @update:page="goToPage"
        >
          <PaginationContent v-slot="{ items }">
            <PaginationPrevious />
            <template
              v-for="(item, index) in items"
              :key="item.type === 'page' ? item.value : `ellipsis-${index}`"
            >
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
      </div>
    </section>
  </div>
</template>
