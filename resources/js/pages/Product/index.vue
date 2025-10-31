<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, destroy, create, edit } from '@/routes/product/index';
import { dashboard } from '@/routes';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue'
import { defineProps } from 'vue';
import { Pencil, Trash, Package } from 'lucide-vue-next';

// --- SHADCN/VUE IMPORTS ---
import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { BreadcrumbItem, Product } from '@/types';

// --- PROPS ---
defineProps<{
  products: Product[]
}>();

// --- LOGIC ---
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: 'Products List',
    href: index().url,
  },
];

const goToCreate = () => router.visit(create().url);

const goToEdit = (id: number) => router.visit(edit(id).url);

</script>

<template>

  <Head title="Products" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <h3
        class="mt-10 scroll-m-20 border-b pb-2 text-2xl font-semibold tracking-tight transition-colors first:mt-0 flex items-center">
        <Package class="h-6 w-6 mr-2 text-indigo-600" />
        Products Catalog
      </h3>
      <Button class="max-w-40" @click="goToCreate">Add new Product</Button>
      <div class="relative min-h-[50vh] flex-1 rounded-xl border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>
                Name
              </TableHead>
              <TableHead>
                REF
              </TableHead>
              <TableHead>
                Category
              </TableHead>
              <TableHead>
                Supplier
              </TableHead>
              <TableHead class="text-right">
                Selling Price
              </TableHead>
              <TableHead class="w-[120px]">
                Actions
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <!-- Check if products list is empty -->
            <TableRow v-if="products.length === 0">
              <TableCell colspan="6" class="text-center py-8 text-gray-500 italic">
                No products found. Click "Add new Product" to get started.
              </TableCell>
            </TableRow>

            <TableRow v-for="product in products" :key="product.id">
              <TableCell class="font-medium">
                {{ product.name }}
              </TableCell>
              <TableCell class="text-xs text-gray-500">
                {{ product.ref }}
              </TableCell>
              <TableCell>
                {{ product.product_category.name }}
              </TableCell>
              <TableCell>
                {{ product.supplier.name }}
              </TableCell>
              <TableCell class="text-right font-semibold text-green-700">
                ${{ product.price }}
              </TableCell>
              <TableCell class="flex items-center space-x-1">
                <Button class="h-8 w-8 p-1 bg-blue-500 hover:bg-blue-600" @click="goToEdit(product.id)">
                  <Pencil class="h-4 w-4" />
                </Button>
                <!-- Using DeleteConfirm component for standard deletion flow -->
                <!-- The mock destroy route is used here -->
                <DeleteConfirm :binded="destroy(product.id)" resource="product" :icon="Trash">
                </DeleteConfirm>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
