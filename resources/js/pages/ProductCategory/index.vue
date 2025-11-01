<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create, edit, destroy } from '@/routes/productCategory/index';
import { ProductCategory, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue';
import { defineProps } from 'vue';
import { Pencil, Trash, Tag, Plus } from 'lucide-vue-next';

// --- SHADCN/VUE IMPORTS ---
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';



// --- PROPS ---
defineProps<{
  categories: ProductCategory[]
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/', // Assuming dashboard route is root
  },
  {
    title: 'Product Categories',
    href: index().url,
  },
];
</script>

<template>

  <Head title="Product Categories" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-8">

      <!-- Header and Action Button -->
      <div class="flex items-center justify-between border-b pb-4">
        <h3 class="flex items-center text-3xl font-bold tracking-tight">
          <Tag class="w-7 h-7 mr-3 " />
          Product Categories
        </h3>
        <Button class="h-10 px-4" @click="router.visit(create().url)">
          <Plus class="w-5 h-5 mr-2" />
          Add Category
        </Button>
      </div>

      <!-- Category Grid -->
      <div v-if="categories.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

        <Card v-for="category in categories" :key="category.id"
          class="shadow-lg hover:shadow-xl transition-shadow border ">
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-xl font-semibold">
              {{ category.name }}
            </CardTitle>
            <Tag class="h-6 w-6 text-emerald-500" />
          </CardHeader>

          <CardContent>
            <CardDescription class="text-sm mb-4">
              ID: #{{ category.id }}
            </CardDescription>

            <Separator class="my-3" />

            <!-- Actions -->
            <div class="flex justify-end space-x-2 pt-2">
              <Button variant="outline" size="icon" @click="router.visit(edit(category.id).url)">
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
          Add New Category
        </Button>
      </div>
    </div>
  </AppLayout>
</template>
