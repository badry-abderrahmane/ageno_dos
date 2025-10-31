<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { store, update, create } from '@/routes/invoice/index'
import { Plus, Trash, DollarSign, Package, FileText, LoaderCircle } from 'lucide-vue-next';

// --- ASSUMED INERTIA/PROJECT IMPORTS ---
import AppLayout from '@/layouts/AppLayout.vue';
// import { create, store, update } from '@/routes/invoice'; // Route helpers
import { dashboard } from '@/routes';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner';

// --- SHADCN/VUE IMPORTS ---
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
import { Separator } from '@/components/ui/separator'
import { Client, Product } from '@/types';


interface InvoiceLineItem {
  id?: number; // Exists if editing an existing line
  product_id: number | null;
  qty: number;
  price: number; // The agreed-upon price for this line item
}
interface Invoice {
  id: number;
  client_id: number;
  total: number;
  status: 'not_paid' | 'paid' | 'partially_paid' | 'cancelled';
  line_items: InvoiceLineItem[];
}
interface BreadcrumbItem { title: string, href: string }


// --- PROPS ---
const props = defineProps<{
  invoice?: Invoice,
  clients: Client[],
  availableProducts: Product[]
}>()


// --- FORM SETUP ---
const isEdit = !!props.invoice;

// Helper to find a product's price by its ID
const getProductPrice = (productId: number | null): number => {
  const product = props.availableProducts.find(p => p.id === productId);
  return product ? parseFloat(product.price) : 0;
};

// Map existing line items or start with a blank one
const initialLineItems = props.invoice?.line_items.map(item => ({
  product_id: item.product_id,
  qty: item.qty,
  price: item.price,
})) || [{ product_id: null, qty: 1, price: 0 }];


const form = useForm({
  client_id: props.invoice?.client_id ?? undefined,
  status: props.invoice?.status ?? 'not_paid',
  // The critical dynamic data structure
  line_items: initialLineItems as InvoiceLineItem[],
});


// --- COMPUTED PROPERTIES ---

const itemSubtotal = (item: InvoiceLineItem): number => {
  // Ensure calculation uses numbers
  return (item.qty || 0) * (item.price || 0);
};

const grandTotal = computed((): number => {
  // Calculate the total by summing all line item subtotals
  return form.line_items.reduce((sum, item) => sum + itemSubtotal(item), 0);
});


// --- LINE ITEM ACTIONS ---

const addLineItem = () => {
  // Add a new blank row with a default quantity of 1 and price of 0
  form.line_items.push({
    product_id: null,
    qty: 1,
    price: 0,
  });
};

const removeLineItem = (index: number) => {
  if (form.line_items.length > 1) {
    form.line_items.splice(index, 1);
  } else {
    // Clear the last row instead of removing it if only one item remains
    Object.assign(form.line_items[0], { product_id: null, qty: 1, price: 0 });
  }
};

const updateItemPrice = (index: number) => {
  const item = form.line_items[index];
  if (item.product_id) {
    // Find the standard price and set it to the line item's price
    item.price = getProductPrice(item.product_id);
  } else {
    item.price = 0;
  }
};


// --- FORM SUBMISSION ---

const submit = () => {
  if (isEdit) {
    form.patch(update(props.invoice!.id).url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Invoice updated successfully.' });
      }
    });
  } else {
    form.post(store().url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Invoice created successfully.' });
        form.reset('client_id', 'status');
        form.line_items = [{ product_id: null, qty: 1, price: 0 }]; // Reset line items cleanly
      }
    });
  }
}

const handleError = (errors: Record<string, string>) => {
  console.error('Validation errors:', errors);
  toast('Error creating/updating invoice', {
    description: 'Please check the form for errors.',
  })
}


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: isEdit ? 'Edit Invoice' : 'Create Invoice',
    href: isEdit ? update(props.invoice!.id).url : create().url,
  },
];
</script>

<template>

  <Head :title="isEdit ? 'Edit Invoice' : 'Create Invoice'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-8">

      <!-- Main Card -->
      <Card class="shadow-lg border-t-4 ">
        <CardHeader>
          <CardTitle class="flex items-center text-2xl font-bold ">
            <FileText class="w-6 h-6 mr-3" />
            {{ isEdit ? 'Edit Invoice' : 'Create New Invoice' }}
          </CardTitle>
          <CardDescription>
            Configure invoice details and line items.
          </CardDescription>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="submit" class="flex flex-col gap-8">

            <!-- --- SECTION 1: HEADER DETAILS --- -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

              <!-- Client Select -->
              <div class="grid gap-2 col-span-1 md:col-span-2">
                <Label for="client_id">Client <span class="text-red-500">*</span></Label>
                <Select id="client_id" name="client_id" v-model:model-value="form.client_id" required
                  :disabled="form.processing">
                  <SelectTrigger :class="{ 'border-red-500': form.errors.client_id }">
                    <SelectValue placeholder="Select a client" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem v-for="client in clients" :key="client.id" :value="client.id">
                        {{ client.name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <InputError :message="form.errors.client_id" />
              </div>

              <!-- Status Select -->
              <div class="grid gap-2">
                <Label for="status">Status <span class="text-red-500">*</span></Label>
                <Select id="status" required name="status" v-model:model-value="form.status"
                  :disabled="form.processing">
                  <SelectTrigger :class="{ 'border-red-500': form.errors.status }">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem value="not_paid"> Not Paid </SelectItem>
                      <SelectItem value="paid"> Paid </SelectItem>
                      <SelectItem value="partially_paid"> Partially Paid </SelectItem>
                      <SelectItem value="cancelled"> Cancelled </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <InputError :message="form.errors.status" />
              </div>
            </div>

            <Separator class="bg-gray-200" />

            <!-- --- SECTION 2: DYNAMIC LINE ITEMS --- -->
            <h3 class="text-xl font-semibold flex items-center">
              <Package class="w-5 h-5 mr-2 " />
              Products & Services
            </h3>

            <!-- Line Item Loop: Now uses consistent Card styling for all devices -->
            <div class="space-y-4">
              <div v-for="(item, index) in form.line_items" :key="index"
                class="relative p-4 border rounded-lg shadow-md grid grid-cols-12 gap-4 items-end">

                <!-- Product Select -->
                <div class="grid gap-2 col-span-12 md:col-span-5">
                  <Label :for="`product_${index}`" class="text-sm font-semibold">Product</Label>
                  <Select :id="`product_${index}`" v-model:model-value="item.product_id"
                    @update:model-value="updateItemPrice(index)" :disabled="form.processing">
                    <SelectTrigger :class="{ 'border-red-500': form.errors[`line_items.${index}.product_id`] }">
                      <SelectValue placeholder="Select a product" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem v-for="product in availableProducts" :key="product.id" :value="product.id">
                          {{ product.name }} ({{ parseFloat(product.price).toFixed(2) }})
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <InputError :message="form.errors[`line_items.${index}.product_id`]" />
                </div>

                <!-- Unit Price Input -->
                <div class="grid gap-2 col-span-6 md:col-span-2">
                  <Label :for="`price_${index}`" class="text-sm font-semibold">Unit Price</Label>
                  <Input :id="`price_${index}`" type="number" v-model.number="item.price" step="0.01" min="0" required
                    :disabled="form.processing" :class="{ 'border-red-500': form.errors[`line_items.${index}.price`] }"
                    class="text-right" />
                  <InputError :message="form.errors[`line_items.${index}.price`]" />
                </div>

                <!-- Quantity Input -->
                <div class="grid gap-2 col-span-6 md:col-span-2">
                  <Label :for="`qty_${index}`" class="text-sm font-semibold">Qty</Label>
                  <Input :id="`qty_${index}`" type="number" v-model.number="item.qty" min="1" required
                    :disabled="form.processing" :class="{ 'border-red-500': form.errors[`line_items.${index}.qty`] }"
                    class="text-right" />
                  <InputError :message="form.errors[`line_items.${index}.qty`]" />
                </div>

                <!-- Line Subtotal & Delete (Combined) -->
                <div class="col-span-12 md:col-span-3 flex justify-between items-center h-full pt-6 md:pt-0">
                  <div class="flex flex-col w-full">
                    <span class="text-xs font-medium text-gray-500 md:hidden">Line Total:</span>
                    <span class="text-lg font-bold md:text-right w-full">
                      ${{ itemSubtotal(item).toFixed(2) }}
                    </span>
                  </div>
                  <Button type="button" @click="removeLineItem(index)" variant="ghost"
                    class="p-2 h-auto text-red-500 hover:text-red-700 shrink-0 ml-4 md:ml-2">
                    <Trash class="w-5 h-5" />
                  </Button>
                </div>
              </div>
            </div>

            <!-- Add Line Item Button -->
            <div class="flex justify-start">
              <Button type="button" variant="outline" @click="addLineItem" :disabled="form.processing" class="">
                <Plus class="w-4 h-4 mr-2" />
                Add Product Line
              </Button>
            </div>

            <Separator class="bg-gray-200 mt-6" />

            <!-- --- SECTION 3: TOTALS AND SUBMIT --- -->
            <div class="flex justify-end">
              <div class="w-full md:w-1/3 space-y-4 p-4 rounded-lg border">
                <div class="flex justify-between items-center text-xl font-bold">
                  <span>Grand Total:</span>
                  <span class="flex items-center text-2xl">
                    <DollarSign class="w-5 h-5 mr-1" />
                    {{ grandTotal.toFixed(2) }}
                  </span>
                </div>

                <Button type="submit" class="w-full h-10 text-lg" :disabled="form.processing">
                  <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin mr-2" />
                  {{ isEdit ? 'Update Invoice' : 'Create Invoice' }}
                </Button>
              </div>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
