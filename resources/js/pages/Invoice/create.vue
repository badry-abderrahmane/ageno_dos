<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { store, update, create, index } from '@/routes/invoice/index'
import apiRoutes from '@/routes/api/index'
import { Plus, Trash, Package, FileText, LoaderCircle, Save } from 'lucide-vue-next';

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
import VirtualSelect from '@/components/VirtualSelect.vue';
import { toMoney } from '@/lib/utils';


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
    item.price = getProductPrice(item.product_id) || 0;
    item.qty = 0;
  } else {
    item.price = 0;
    item.qty = 0;
  }
};


// --- FORM SUBMISSION ---

const submit = () => {
  if (isEdit) {
    form.patch(update(props.invoice!.id).url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Facture mise à jour avec succès.' });
      }
    });
  } else {
    form.post(store().url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Facture crée avec succès.' });
        form.reset('client_id', 'status');
        form.line_items = [{ product_id: null, qty: 1, price: 0 }]; // Reset line items cleanly
      }
    });
  }
}

const handleError = (errors: Record<string, string>) => {
  console.error('Validation errors:', errors);
  toast('Error Facture', {
    description: 'SVP Vérifier le formulaire',
  })
}


const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Tableau de bord',
    href: dashboard().url,
  },
  {
    title: 'Factures',
    href: index().url,
  },
  {
    title: isEdit ? 'Enregistrer' : 'Créer Facture',
    href: isEdit ? update(props.invoice!.id).url : create().url,
  },
];
</script>

<template>

  <Head :title="isEdit ? 'Edition Facture' : 'Creation Facture'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-2 md:p-4 pb-16">

      <!-- Main Card -->
      <Card class="shadow-none md:border">
        <CardHeader class="px-3 md:px-6">
          <CardTitle class="flex items-center text-2xl font-bold ">
            <FileText class="w-6 h-6 mr-3" />
            {{ isEdit ? 'Edition Facture' : 'Creation Facture' }}
          </CardTitle>
          <CardDescription>
            Configurer les informations facture.
          </CardDescription>
        </CardHeader>

        <CardContent class="px-3 md:px-6">
          <form @submit.prevent="submit" class="flex flex-col gap-8">

            <!-- --- SECTION 1: HEADER DETAILS --- -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

              <!-- Client Select -->
              <div class="grid gap-2 col-span-1 md:col-span-2">
                <VirtualSelect label="Client" placeholder="Select a client" :fetch-url="apiRoutes.clients().url"
                  v-model:model-value="form.client_id" id="client_id" :error="form.errors.client_id"
                  :disabled="form.processing" />
                <!-- <InputError :message="form.errors.client_id" /> -->
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
                      <SelectItem value="not_paid"> Non payé </SelectItem>
                      <SelectItem value="paid"> Payé </SelectItem>
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
              Produits & Services
            </h3>

            <!-- Line Item Loop: Now uses consistent Card styling for all devices -->
            <div class="space-y-4">
              <div v-for="(item, index) in form.line_items" :key="index"
                class="relative p-4 border rounded-lg shadow-md grid grid-cols-12 gap-4 items-end">

                <!-- Product Select -->
                <div class="grid gap-2 col-span-12 md:col-span-5">
                  <VirtualSelect :label="`Produit - ${index + 1}`" placeholder="Select a product"
                    :fetch-url="apiRoutes.products().url" v-model:model-value="item.product_id" :id="`product_${index}`"
                    @update:model-value="updateItemPrice(index)" :error="form.errors[`line_items.${index}.product_id`]"
                    :disabled="form.processing" />
                </div>

                <!-- Unit Price Input -->
                <div class="grid gap-2 col-span-6 md:col-span-2">
                  <Label :for="`price_${index}`" class="text-sm font-semibold">Prix unitaire</Label>
                  <Input :id="`price_${index}`" type="number" v-model.number="item.price" step="0.01" min="0" required
                    :disabled="form.processing" :class="{ 'border-red-500': form.errors[`line_items.${index}.price`] }"
                    class="text-right" />
                  <InputError :message="form.errors[`line_items.${index}.price`]" />
                </div>

                <!-- Quantity Input -->
                <div class="grid gap-2 col-span-6 md:col-span-2">
                  <Label :for="`qty_${index}`" class="text-sm font-semibold">Quantité</Label>
                  <Input :id="`qty_${index}`" type="number" v-model.number="item.qty" min="1" required
                    :disabled="form.processing" :class="{ 'border-red-500': form.errors[`line_items.${index}.qty`] }"
                    class="text-right" />
                  <InputError :message="form.errors[`line_items.${index}.qty`]" />
                </div>

                <!-- Line Subtotal & Delete (Combined) -->
                <div class="col-span-12 md:col-span-3 flex justify-between items-center h-full pt-6 md:pt-0">
                  <div class="flex flex-col w-full">
                    <span class="text-xs font-medium text-gray-500 md:hidden">Total:</span>
                    <span class="text-lg italic font-mono font-bold md:text-right w-full">
                      {{ itemSubtotal(item).toFixed(2) }}
                    </span>
                  </div>
                  <Button type="button" @click="removeLineItem(index)" variant="ghost"
                    class="p-2 h-auto text-red-500 hover:text-red-700 shrink-0 ml-4 md:ml-2">
                    <Trash class="w-10 h-10" />
                  </Button>
                </div>
              </div>
            </div>

            <!-- Add Line Item Button -->
            <div class="flex justify-start">
              <Button type="button" variant="outline" @click="addLineItem" :disabled="form.processing" class="">
                <Plus class="w-4 h-4 mr-2" />
                Ajouter un produit
              </Button>
            </div>

            <Separator class="bg-gray-200 mt-6" />

            <!-- --- SECTION 3: TOTALS AND SUBMIT --- -->
            <div class="fixed bottom-0 left-0 md:static bg-foreground md:bg-background w-full flex justify-end">
              <div class="w-full md:w-1/2 flex justify-between items-center md:space-y-4 p-4 rounded-lg md:border">
                <div class="flex flex-col justify-end text-right font-bold">
                  <span class="flex items-center text-gray-400 font-mono">
                    TTC {{ toMoney(grandTotal + (grandTotal * 0.2)) }}
                  </span>
                  <span class="flex items-center text-2xl text-background md:text-foreground font-mono">
                    {{ toMoney(grandTotal) }}
                  </span>
                </div>

                <Button type="submit" variant="secondary" class="ml-2 md:ml-0 text-lg w-1/3 md:w-1/2"
                  :disabled="form.processing">
                  <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin mr-2" />
                  <Save class="size-6" v-else />
                  <span class="hidden md:block">{{ isEdit ? 'Enregistrer' : 'Créer facture' }}</span>
                </Button>
              </div>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
