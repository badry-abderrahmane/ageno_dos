<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create, edit, destroy } from '@/routes/supplier/index';
import { dashboard } from '@/routes';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue'
import { defineProps, ref, watch } from 'vue';
import { Pencil, Trash, Search, Factory, Plus } from 'lucide-vue-next';
import { debounce } from 'lodash';

// Import necessary shadcn-vue components
import {
  Card,
  CardHeader,
  CardTitle,
  CardContent,
  CardFooter,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import { Link } from '@inertiajs/vue3' // Import Link for pagination

import { BreadcrumbItem, Supplier, type InertiaLink } from '@/types';
import PageHeader from '@/components/PageHeader.vue';


// --- PROPS ---
// Updated props to receive a paginated structure and filters
const props = defineProps<{
  suppliers: {
    data: Supplier[],
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
    title: 'Tableau de bord',
    href: dashboard().url,
  },
  {
    title: 'Fournisseurs',
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

  <Head title="Suppliers" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <PageHeader title="Fournisseurs" :icon="Factory"></PageHeader>

      <!-- Search and Add new bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full max-w-sm">
          <Input type="text" placeholder="Recherche..." v-model="search" class="pl-10" />
          <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        </div>
        <Button class="w-full sm:w-auto sm:max-w-40" @click="goToCreate">
          <Plus /> Ajouter
        </Button>
      </div>


      <div class="relative flex-1 rounded-xl">

        <!-- Responsive Card Grid -->
        <div v-if="props.suppliers.data.length > 0"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <Card v-for="supplier in props.suppliers.data" :key="supplier.id" class="flex flex-col justify-between">
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle class="text-lg">{{ supplier.name }}</CardTitle>
              <Factory class="h-6 w-6 text-foreground" />
            </CardHeader>

            <CardContent class="text-sm space-y-1">
              <!-- <p class="text-muted-foreground">
                Email: <span class="text-foreground font-medium">{{ supplier.email ?? 'N/A' }}</span>
              </p>
              <p class="text-muted-foreground">
                Phone: <span class="text-foreground font-medium">{{ supplier.phone ?? 'N/A' }}</span>
              </p> -->
            </CardContent>

            <CardFooter class="flex justify-end gap-2">
              <Button variant="outline" size="icon" @click="goToEdit(supplier.id)">
                <Pencil class="h-4 w-4" />
              </Button>
              <DeleteConfirm :binded="destroy.form(supplier.id)" resource="supplier" :icon="Trash">
              </DeleteConfirm>
            </CardFooter>
          </Card>
        </div>

        <!-- "No suppliers" message -->
        <div v-if="props.suppliers.data.length === 0"
          class="flex items-center justify-center py-20 text-muted-foreground">
          No suppliers found.
        </div>

        <!-- Pagination -->
        <div class="p-4 flex justify-center mt-6" v-if="props.suppliers.last_page > 1">
          <div class="flex flex-wrap justify-center space-x-2">
            <template v-for="(link, index) in props.suppliers.links" :key="index">
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
