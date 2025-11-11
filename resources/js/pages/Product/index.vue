<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, destroy, create, edit } from '@/routes/product/index';
import { dashboard } from '@/routes';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue'
import { defineProps, ref, watch } from 'vue';
import { Pencil, Trash, Package, Search, DollarSign, Factory, Boxes } from 'lucide-vue-next';
import { debounce } from 'lodash';

// --- SHADCN/VUE IMPORTS ---
import {
  Card,
  CardTitle,
  CardContent,
  CardFooter,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import { Link } from '@inertiajs/vue3' // Import Link for pagination

import { BreadcrumbItem, Product, type InertiaLink } from '@/types';
import PageHeader from '@/components/PageHeader.vue';
import { toMoney } from '@/lib/utils';

// --- PROPS ---
// Updated props to receive a paginated structure and filters
const props = defineProps<{
  products: {
    data: Product[],
    links: InertiaLink[], // Laravel's pagination links object
    last_page: number
  };
  filters: {
    search: string | null;
  };
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

// State for the search input
const search = ref(props.filters.search || '');

// Watch for changes in the search input and perform a visit
watch(search, debounce((value) => {
  // Perform an Inertia GET request
  router.get(
    index().url,
    { search: value }, // Pass the search query as data
    {
      preserveState: true, // Preserve the component state
      replace: true, // Replace the current history state
    }
  );
}, 300));

// Function to check if a pagination link is active/current page
const isActiveLink = (link: InertiaLink) => {
  return link.active && link.label !== '...';
};

const goToCreate = () => router.visit(create().url);

const goToEdit = (id: number) => router.visit(edit(id).url);

</script>

<template>

  <Head title="Products" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <PageHeader title="Products Catalog" :icon="Package"></PageHeader>

      <!-- Search and Add new bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full max-w-sm">
          <Input type="text" placeholder="Search products (name, ref, supplier, category)..." v-model="search"
            class="pl-10" />
          <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        </div>
        <Button class="w-full sm:w-auto sm:max-w-40" @click="goToCreate">Add new Product</Button>
      </div>


      <div class="relative flex-1 rounded-xl">

        <!-- Responsive Card Grid -->
        <div v-if="props.products.data.length > 0"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <Card v-for="product in props.products.data" :key="product.id" class="flex flex-col justify-between">
            <CardContent class="text-sm space-y-2">
              <CardTitle class="text-md">{{ product.name }}</CardTitle>
              <p class="flex items-center text-muted-foreground">
                <Boxes class="h-4 w-4 mr-2" />
                Category: <span class="ml-1 font-medium text-foreground">{{ product.product_category.name }}</span>
              </p>
              <p class="flex items-center text-muted-foreground">
                <Factory class="h-4 w-4 mr-2" />
                Supplier: <span class="ml-1 font-medium text-foreground">{{ product.supplier.name }}</span>
              </p>
            </CardContent>

            <CardFooter class="flex justify-end items-center gap-2">
              <p v-if="product.price" class="mr-auto flex items-center text-lg font-bold text-green-600 pt-2">
                {{ toMoney(+product.price) }}
              </p>

              <Button variant="outline" size="icon" @click="goToEdit(product.id)">
                <Pencil class="h-4 w-4" />
              </Button>
              <DeleteConfirm :binded="destroy.form(product.id)" resource="product" :icon="Trash">
              </DeleteConfirm>
            </CardFooter>
          </Card>
        </div>

        <!-- "No products" message -->
        <div v-if="props.products.data.length === 0"
          class="flex items-center justify-center py-20 text-muted-foreground">
          No products found.
        </div>

        <!-- Pagination -->
        <div class="p-4 flex justify-center mt-6" v-if="props.products.last_page > 1">
          <div class="flex flex-wrap justify-center space-x-2">
            <template v-for="(link, index) in props.products.links" :key="index">
              <Link v-if="link.url" :href="link.url" preserve-scroll preserve-state
                class="px-3 py-1 text-sm border rounded mb-2" :class="{
                  'bg-primary text-primary-foreground border-primary': isActiveLink(link),
                  'hover:bg-accent hover:text-accent-foreground': !isActiveLink(link),
                  'text-muted-foreground border-border': link.label === '...'
                }"><span v-html="link.label"></span></Link>
              <span v-else class="px-3 py-1 text-sm border rounded mb-2 text-muted-foreground border-border"
                v-html="link.label" />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
