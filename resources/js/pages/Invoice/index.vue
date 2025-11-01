<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create, edit, destroy, download } from '@/routes/invoice';
import { Invoice, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue'
import { defineProps } from 'vue';
import { Pencil, Trash } from 'lucide-vue-next';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import Button from '@/components/ui/button/Button.vue';

defineProps<{
  invoices: Invoice[]
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: index().url,
  },
];
</script>

<template>

  <Head title="Invoices" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <h3 class="mt-10 scroll-m-20 border-b pb-2 text-2xl font-semibold tracking-tight transition-colors first:mt-0">
        Invoices list
      </h3>
      <Button class="max-w-40" @click="router.visit(create())">Add new</Button>
      <div
        class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>
                Client
              </TableHead>
              <TableHead>
                Total
              </TableHead>
              <TableHead>
                Status
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="invoice in invoices" :key="invoice.id">
              <TableCell class="font-medium">
                {{ invoice.client.name }}
              </TableCell>
              <TableCell>{{ invoice.total }}</TableCell>
              <TableCell>{{ invoice.status }}</TableCell>
              <TableCell>
                <Button class="mr-1" @click="router.visit(edit(invoice.id).url)">
                  <Pencil />
                </Button>
                <a :href="download({ invoice: invoice.id }).url">
                  <Button variant="outline">Download PDF</Button>
                </a>
                <DeleteConfirm :binded="destroy.form(invoice.id)" resource="invoice" :icon="Trash">
                </DeleteConfirm>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
