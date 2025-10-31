<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create, edit, destroy } from '@/routes/supplier/index';
import { dashboard } from '@/routes';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue'
import { defineProps } from 'vue';
import { Pencil, Trash, Truck } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { BreadcrumbItem, Supplier } from '@/types';


// --- PROPS ---
defineProps<{
  suppliers: Supplier[]
}>();


// --- LOGIC ---
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: 'Suppliers List',
    href: index().url,
  },
];

const goToCreate = () => router.visit(create().url);

const goToEdit = (id: number) => router.visit(edit(id).url);

</script>

<template>

  <Head title="Suppliers" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <h3
        class="mt-10 scroll-m-20 border-b pb-2 text-2xl font-semibold tracking-tight transition-colors first:mt-0 flex items-center">
        <Truck class="h-6 w-6 mr-2 text-indigo-600" />
        Suppliers List
      </h3>
      <Button class="max-w-40" @click="goToCreate">Add new Supplier</Button>
      <div class="relative min-h-[50vh] flex-1 rounded-xl border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>
                Company Name
              </TableHead>
              <TableHead>
                Email
              </TableHead>
              <TableHead>
                Phone
              </TableHead>
              <TableHead class="w-[120px]">
                Actions
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <!-- Check if suppliers list is empty -->
            <TableRow v-if="suppliers.length === 0">
              <TableCell colspan="5" class="text-center py-8 text-gray-500 italic">
                No suppliers found. Click "Add new Supplier" to get started.
              </TableCell>
            </TableRow>

            <TableRow v-for="supplier in suppliers" :key="supplier.id">
              <TableCell class="font-medium">
                {{ supplier.name }}
              </TableCell>
              <TableCell>
                {{ supplier.email ?? 'N/A' }}
              </TableCell>
              <TableCell>
                {{ supplier.phone ?? 'N/A' }}
              </TableCell>
              <TableCell class="flex items-center space-x-1">
                <Button class="h-8 w-8 p-1 bg-blue-500 hover:bg-blue-600" @click="goToEdit(supplier.id)">
                  <Pencil class="h-4 w-4" />
                </Button>
                <!-- Using DeleteConfirm component for standard deletion flow -->
                <DeleteConfirm :binded="destroy.form(supplier.id)" resource="supplier" :icon="Trash">
                </DeleteConfirm>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
