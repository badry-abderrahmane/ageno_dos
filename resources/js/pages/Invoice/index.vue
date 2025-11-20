<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create, edit, destroy, download } from '@/routes/invoice';
import { Invoice, type BreadcrumbItem, type InertiaLink } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue'
import { defineProps, ref, watch } from 'vue';
// Import new icons and utilities
import { Pencil, Trash, Search, Download, File, FileText, Truck, CirclePlus } from 'lucide-vue-next';
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
// Import Input for search
import Input from '@/components/ui/input/Input.vue';
// Import Link for pagination
import { Link } from '@inertiajs/vue3'
import Badge from '@/components/ui/badge/Badge.vue';
import PageHeader from '@/components/PageHeader.vue';
import { dashboard } from '@/routes';
import { toMoney } from '@/lib/utils';

// Extend the props to receive a paginated structure and filters
const props = defineProps<{
  invoices: {
    data: Invoice[],
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
  },
  {
    title: 'Factures',
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

  <Head title="Invoices" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <PageHeader title="Factures" :icon="FileText"></PageHeader>

      <!-- Search and Add new bar -->
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
        <div v-if="invoices.data.length > 0" class="grid grid-cols-1  lg:grid-cols-2 xl:grid-cols-3 gap-4">
          <Card v-for="(invoice, index) in invoices.data" :key="invoice.id" :class="`delay-${index * 1000}`"
            class="animate-in fade-in slide-in-from-top-12 duration-500 fill-mode-both flex flex-col justify-between">
            <CardHeader>
              <CardTitle>{{ invoice.client.name }}</CardTitle>
              <CardDescription>
                <Badge v-if="invoice.status === 'paid'" variant="success">
                  {{ 'Payé' }}
                </Badge>
                <Badge v-else variant="destructive">
                  {{ 'Non payé' }}
                </Badge>
              </CardDescription>
              <p class="text-lg text-gray-600 font-mono font-semibold italic text-right pt-2">{{ toMoney(+invoice.total)
              }}</p>
            </CardHeader>
            <CardFooter class="flex justify-end gap-2">
              <Button variant="outline" size="xl" @click="router.visit(edit(invoice.id).url)">
                <Pencil />
              </Button>
              <a :href="download({ invoice: invoice.id }, { query: { type: 'quote' } }).url">
                <Button variant="outline" size="xl">
                  <File />
                </Button>
              </a>
              <a :href="download({ invoice: invoice.id }, { query: { type: 'delivery' } }).url">
                <Button variant="outline" size="xl">
                  <Truck />
                </Button>
              </a>
              <a :href="download({ invoice: invoice.id }, { query: { type: 'invoice' } }).url">
                <Button variant="outline" size="xl">
                  <Download />
                </Button>
              </a>
              <DeleteConfirm :binded="destroy.form(invoice.id)" resource="invoice" :icon="Trash">
              </DeleteConfirm>
            </CardFooter>
          </Card>
        </div>

        <!-- "No invoices" message -->
        <div v-if="invoices.data.length === 0" class="flex items-center justify-center py-20 text-muted-foreground">
          No invoices found.
        </div>

        <!-- Pagination -->
        <div class="p-4 flex justify-center mt-6" v-if="invoices.last_page > 1">
          <div class="flex flex-wrap justify-center space-x-2">
            <template v-for="(link, index) in invoices.links" :key="index">
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
