<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Tag, LoaderCircle } from 'lucide-vue-next';

// --- ASSUMED INERTIA/PROJECT IMPORTS ---
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store, update, create } from '@/routes/productCategory'; // Route helpers
import { dashboard } from '@/routes';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner';

// --- SHADCN/VUE IMPORTS ---
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { BreadcrumbItem, ProductCategory } from '@/types';


// --- PROPS ---
const props = defineProps<{
  productCategory?: ProductCategory,
}>()


// --- FORM SETUP ---
const isEdit = !!props.productCategory;

const form = useForm({
  name: props.productCategory?.name ?? '',
});


// --- FORM SUBMISSION ---

const submit = () => {
  if (isEdit) {
    // Assuming route naming 'product-category.update'
    form.patch(update(props.productCategory!.id).url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Product Category updated successfully.' });
      }
    });
  } else {
    // Assuming route naming 'product-category.store'
    form.post(store().url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Product Category created successfully.' });
        form.reset('name');
      }
    });
  }
}

const handleError = (errors: Record<string, string>) => {
  console.error('Validation errors:', errors);
  toast('Error', {
    description: 'Please check the form for errors.',
    action: {
      label: 'Dismiss',
      onClick: () => { }
    }
  })
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: 'Categories',
    href: index().url,
  },
  {
    title: isEdit ? 'Edit' : 'Create',
    href: isEdit ? update(props.productCategory!.id).url : create().url,
  },
];
</script>

<template>

  <Head :title="isEdit ? 'Edit Category' : 'Create Category'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex justify-center p-4 md:p-8">

      <!-- Main Card for Form -->
      <Card class="w-full max-w-lg shadow-xl border-t-4">
        <CardHeader>
          <CardTitle class="flex items-center text-2xl font-bold">
            <Tag class="w-6 h-6 mr-3" />
            {{ isEdit ? 'Edit Product Category' : 'Create New Product Category' }}
          </CardTitle>
          <CardDescription>
            Use a clear, descriptive name for organizing products.
          </CardDescription>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="submit" class="flex flex-col gap-6">

            <!-- Category Name Input -->
            <div class="grid gap-2">
              <Label for="name">Category Name <span class="text-red-500">*</span></Label>
              <Input id="name" type="text" v-model="form.name" required autofocus :disabled="form.processing"
                :class="{ 'border-red-500': form.errors.name }" placeholder="e.g., Electronics, Beverages, Software" />
              <InputError :message="form.errors.name" />
            </div>

            <!-- Submit Button -->
            <Button type="submit" class="w-full h-10 text-lg" :disabled="form.processing">
              <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin mr-2" />
              {{ isEdit ? 'Update Category' : 'Create Category' }}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
