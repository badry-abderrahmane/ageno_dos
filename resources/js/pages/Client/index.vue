<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create, edit, destroy } from '@/routes/client';
import { Client, type BreadcrumbItem, type InertiaLink } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue'
import { defineProps, ref, watch } from 'vue';
import { Pencil, Trash, Search, Briefcase, CirclePlus } from 'lucide-vue-next';
import { debounce } from 'lodash';

// Import Card components from shadcn-vue
import {
  Card,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { Link } from '@inertiajs/vue3' // Import Link for pagination
import PageHeader from '@/components/PageHeader.vue';
import { dashboard } from '@/routes';

// Extend the props to receive a paginated structure and filters
const props = defineProps<{
  clients: {
    data: Client[],
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
    href: dashboard().url,
  }, {
    title: 'Clients',
    href: index().url,
  },
];

// State for the search input
const search = ref(props.filters.search || '');

// Watch for changes in the search input and perform a visit
// We use debounce to limit how often we hit the backend
watch(search, debounce((value) => {
  // Perform an Inertia GET request
  router.get(
    index().url,
    { search: value }, // Pass the search query as data
    {
      preserveState: true, // Preserve the component state (like scroll position)
      replace: true, // Replace the current history state instead of adding a new one
    }
  );
}, 300));

// Function to check if a pagination link is active/current page
const isActiveLink = (link: InertiaLink) => {
  // Link label might be '...' which should not be active
  return link.active && link.label !== '...';
};
</script>

<template>

  <Head title="Clients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <PageHeader title="Clients" :icon="Briefcase"></PageHeader>
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full max-w-sm">
          <Input type="text" placeholder="Recherche..." v-model="search" class="pl-10" />
          <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        </div>
        <Button class="w-full sm:w-auto sm:max-w-40" @click="router.visit(create())">
          <CirclePlus></CirclePlus>
          AJOUTER
        </Button>
      </div>

      <!-- Main content area -->
      <div class="relative flex-1 rounded-xl border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">

        <!-- Grid layout for cards -->
        <div v-if="clients.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          <Card v-for="client in clients.data" :key="client.id" class="flex flex-col justify-between">
            <CardHeader>
              <CardTitle>{{ client.name }}</CardTitle>
              <CardDescription>ICE: {{ client.ice }}</CardDescription>
            </CardHeader>
            <CardFooter class="flex justify-end gap-2">
              <Button variant="outline" size="icon" class="mr-1" @click="router.visit(edit(client.id).url)">
                <Pencil class="h-4 w-4" />
              </Button>
              <DeleteConfirm :binded="destroy.form(client.id)" resource="client" :icon="Trash">
              </DeleteConfirm>
            </CardFooter>
          </Card>
        </div>

        <!-- "No clients" message -->
        <div v-if="clients.data.length === 0" class="flex items-center justify-center py-20 text-muted-foreground">
          No clients found.
        </div>

        <!-- Pagination -->
        <div class="p-4 flex justify-center mt-6" v-if="clients.last_page > 1">
          <div class="flex flex-wrap justify-center space-x-2">
            <template v-for="(link, index) in clients.links" :key="index">
              <Link v-if="link.url" :href="link.url" preserve-scroll preserve-state
                class="px-3 py-1 text-sm border rounded mb-2" :class="{
                  'bg-primary text-primary-foreground border-primary': isActiveLink(link),
                  'hover:bg-accent hover:text-accent-foreground': !isActiveLink(link),
                  'text-muted-foreground border-border': link.label === '...' // Special styling for '...'
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
