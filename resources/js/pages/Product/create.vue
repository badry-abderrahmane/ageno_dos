<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { store, create, update } from '@/routes/product/index';
import { dashboard } from '@/routes';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { toast } from 'vue-sonner';

// --- TYPE DEFINITIONS (Simulated) ---
interface Supplier { id: number, name: string }
interface ProductCategory { id: number, name: string }
interface Product {
  id: number;
  name: string;
  ref: string;
  delivery_time: string;
  supplier_price: string;
  price: string;
  min_qty: string;
  max_qty: string;
  note: string;
  img: string;
  product_category_id: number;
  supplier_id: number;
}
interface BreadcrumbItem { title: string, href: string }

const props = defineProps<{
  product?: Product,
  suppliers: Supplier[],
  categories: ProductCategory[],
}>()


// --- INERTIA FORM LOGIC SIMULATION ---
const isEdit = !!props.product;

// Simulating the useForm structure:
const form = useForm({
  name: props.product?.name ?? '',
  delivery_time: props.product?.delivery_time ?? '0',
  supplier_price: props.product?.supplier_price ?? '0.00',
  price: props.product?.price ?? '0.00',
  min_qty: props.product?.min_qty ?? '0',
  max_qty: props.product?.max_qty ?? '0',
  note: props.product?.note ?? '',
  img: props.product?.img ?? '',
  product_category_id: props.product?.product_category_id ?? null as number | null,
  supplier_id: props.product?.supplier_id ?? null as number | null,
});

const handleError = (validationErrors: Record<string, string>) => {
  console.error('Validation errors:', validationErrors);
  toast('Error creating/updating product', {
    description: 'Please check the form for errors.',
  })
}

const submit = () => {
  if (isEdit) {
    form.patch(update.url(props.product!.id), {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Product updated successfully.' });
      }
    });
  } else {
    form.post(store().url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Product created successfully.' });
      }
    });
  }
}

// --- BREADCRUMBS ---
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: isEdit ? 'Edit Product' : 'Create Product',
    href: isEdit ? update(props.product!.id).url : create().url,
  },
];

</script>

<template>

  <Head :title="isEdit ? 'Edit Product' : 'Create Product'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle> {{ isEdit ? 'Edit Product' : 'Create New Product' }}</CardTitle>
          <CardDescription>Identification, pricing, and vendor details.</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="flex flex-col gap-6">

            <!-- --- Section 1: Core Product Details --- -->
            <h3 class="text-lg font-semibold border-b pb-1">
              Product Identification
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="grid gap-2">
                <Label for="name">Name <span class="text-red-500">*</span></Label>
                <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="off" name="name"
                  placeholder="Ultra-Max Sensor V2" v-model="form.name" :disabled="form.processing"
                  :class="{ 'border-red-500': form.errors.name }" />
                <InputError :message="form.errors.name" />
              </div>
            </div>

            <!-- --- Section 2: Relationships (Dropdowns) --- -->
            <h3 class="text-lg font-semibold border-b pb-1 pt-2">
              Category & Vendor
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Product Category Select -->
              <div class="grid gap-2">
                <Label for="product_category_id">Product Category <span class="text-red-500">*</span></Label>
                <Select id="product_category_id" name="product_category_id"
                  v-model:model-value="form.product_category_id" :disabled="form.processing">
                  <SelectTrigger :class="{ 'border-red-500': form.errors.product_category_id }">
                    <SelectValue placeholder="Select a category" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <InputError :message="form.errors.product_category_id" />
              </div>

              <!-- Supplier Select -->
              <div class="grid gap-2">
                <Label for="supplier_id">Supplier <span class="text-red-500">*</span></Label>
                <Select id="supplier_id" name="supplier_id" v-model:model-value="form.supplier_id"
                  :disabled="form.processing">
                  <SelectTrigger :class="{ 'border-red-500': form.errors.supplier_id }">
                    <SelectValue placeholder="Select a supplier" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                        {{ supplier.name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <InputError :message="form.errors.supplier_id" />
              </div>
            </div>

            <!-- --- Section 3: Pricing and Logistics --- -->
            <h3 class="text-lg font-semibold border-b pb-1 pt-2">
              Pricing & Stock
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              <div class="grid gap-2">
                <Label for="supplier_price">Supplier Price</Label>
                <Input id="supplier_price" type="number" step="0.01" min="0" :tabindex="5" name="supplier_price"
                  placeholder="0.00" v-model="form.supplier_price" :disabled="form.processing"
                  :class="{ 'border-red-500': form.errors.supplier_price }" />
                <InputError :message="form.errors.supplier_price" />
              </div>

              <div class="grid gap-2">
                <Label for="price">Selling Price <span class="text-red-500">*</span></Label>
                <Input id="price" type="number" step="0.01" min="0" required :tabindex="6" name="price"
                  placeholder="0.00" v-model="form.price" :disabled="form.processing"
                  :class="{ 'border-red-500': form.errors.price }" />
                <InputError :message="form.errors.price" />
              </div>

              <div class="grid gap-2">
                <Label for="min_qty">Min Order Qty</Label>
                <Input id="min_qty" type="number" min="0" :tabindex="7" name="min_qty" placeholder="0"
                  v-model="form.min_qty" :disabled="form.processing"
                  :class="{ 'border-red-500': form.errors.min_qty }" />
                <InputError :message="form.errors.min_qty" />
              </div>

              <div class="grid gap-2">
                <Label for="max_qty">Max Order Qty</Label>
                <Input id="max_qty" type="number" min="0" :tabindex="8" name="max_qty" placeholder="0"
                  v-model="form.max_qty" :disabled="form.processing"
                  :class="{ 'border-red-500': form.errors.max_qty }" />
                <InputError :message="form.errors.max_qty" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="grid gap-2">
                <Label for="img">Image URL/Path</Label>
                <Input id="img" type="text" :tabindex="9" name="img" placeholder="/images/sensor_v2.jpg"
                  v-model="form.img" :disabled="form.processing" :class="{ 'border-red-500': form.errors.img }" />
                <InputError :message="form.errors.img" />
              </div>
              <div class="grid gap-2">
                <Label for="delivery_time">Delivery Time (Days)</Label>
                <Input id="delivery_time" type="number" min="0" :tabindex="10" name="delivery_time" placeholder="0"
                  v-model="form.delivery_time" :disabled="form.processing"
                  :class="{ 'border-red-500': form.errors.delivery_time }" />
                <InputError :message="form.errors.delivery_time" />
              </div>
            </div>

            <!-- --- Section 4: Notes --- -->
            <h3 class="text-lg font-semibold border-b pb-1 pt-2">
              Additional Information
            </h3>
            <div class="grid gap-2">
              <Label for="note">Product Note/Description</Label>
              <textarea id="note" rows="3"
                class="w-full p-2.5 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm"
                v-model="form.note" placeholder="Any internal notes or detailed description..." :tabindex="11"
                :disabled="form.processing" :class="{ 'border-red-500': form.errors.note }"></textarea>
              <InputError :message="form.errors.note" />
            </div>

            <Button type="submit" class="mt-2 w-full" tabindex="12" :disabled="form.processing">
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
              {{ isEdit ? 'Update Product' : 'Create Product' }}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
