<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create, edit, destroy } from '@/routes/productCategory/index';
import { ProductCategory, type BreadcrumbItem, type InertiaLink } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue';
import { defineProps, ref, watch } from 'vue';
import { Pencil, Trash, Tag, CirclePlus, Search, Boxes } from 'lucide-vue-next';
import { debounce } from 'lodash';

// --- SHADCN/VUE IMPORTS ---
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import { Link } from '@inertiajs/vue3' // Import Link for pagination
import PageHeader from '@/components/PageHeader.vue';
import { dashboard } from '@/routes';


// --- PROPS ---
// Updated props to receive a paginated structure and filters
const props = defineProps<{
  categories: {
    data: ProductCategory[],
    links: InertiaLink[], // Laravel's pagination links object
    last_page: number
  };
  filters: {
    search: string | null;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Tableau de bord',
    href: dashboard.url(), // Assuming dashboard route is root
  },
  {
    title: 'Catégories',
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
</script>

<template>

  <Head title="Catégories" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-2 md:p-4">
      <PageHeader title="Categories" :icon="Boxes"></PageHeader>

      <!-- Search and Add new bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="relative w-full max-w-sm">
          <Input type="text" placeholder="Recherche..." v-model="search" class="pl-10" />
          <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        </div>
        <Button class="w-full sm:w-auto sm:max-w-40" @click="router.visit(create().url)">
          <CirclePlus></CirclePlus>
          AJOUTER
        </Button>
      </div>

      <!-- Category Grid -->
      <div class="relative flex-1">
        <div v-if="props.categories.data.length > 0"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

          <Card v-for="category in props.categories.data" :key="category.id"
            class="border flex flex-col justify-between">
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle class="text-lg font-semibold">
                {{ category.name }}
              </CardTitle>
              <Boxes class="h-6 w-6 text-foreground" />
            </CardHeader>

            <CardContent>
              <!-- Actions -->
              <div class="flex justify-end space-x-2 pt-2">
                <Button variant="outline" size="xl" @click="router.visit(edit(category.id).url)">
                  <Pencil class="w-4 h-4" />
                </Button>

                <DeleteConfirm :binded="destroy.form(category.id)" resource="product-category" :icon="Trash">
                </DeleteConfirm>
              </div>
            </CardContent>
          </Card>

        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-lg border border-dashed">
          <Tag class="w-12 h-12 text-gray-400 mb-4" />
          <p class="text-xl font-semibold text-gray-600">No categories found.</p>
          <p class="text-gray-500 mb-6">Start by adding your first product category.</p>
          <Button class="h-10 px-6 bg-emerald-600 hover:bg-emerald-700" @click="router.visit(create().url)">
            <Plus class="w-5 h-5 mr-2" />
            Ajouter
          </Button>
        </div>

        <!-- Pagination -->
        <div class="p-4 flex justify-center mt-6" v-if="props.categories.last_page > 1">
          <div class="flex flex-wrap justify-center space-x-2">
            <template v-for="(link, index) in props.categories.links" :key="index">
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
