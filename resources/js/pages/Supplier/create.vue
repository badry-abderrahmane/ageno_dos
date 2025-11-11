<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { create, index, store, update } from '@/routes/supplier/index';
import { dashboard } from '@/routes';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { toast } from 'vue-sonner'
import { BreadcrumbItem, Supplier } from '@/types';


// --- PROPS ---
const props = defineProps<{
  supplier?: Supplier,
}>()

// --- FORM SETUP ---
const isEdit = !!props.supplier;

const form = useForm({
  name: props.supplier?.name ?? '',
  email: props.supplier?.email ?? '',
  phone: props.supplier?.phone ?? '',
  address: props.supplier?.address ?? '',
  contact: props.supplier?.contact ?? '',
  category: props.supplier?.category ?? '',
});


// --- ACTIONS ---
const submit = () => {
  if (isEdit) {
    form.patch(update(props.supplier!.id).url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Supplier updated successfully.' });
      }
    });
  } else {
    form.post(store().url, {
      onError: handleError,
      onSuccess: () => {
        toast('Success', { description: 'Supplier created successfully.' });
      }
    });
  }
}

const handleError = (errors: Record<string, string>) => {
  console.error('Validation errors:', errors);
  toast('Error creating/updating supplier', {
    description: 'Please check the form for errors.',
  })
}


// --- BREADCRUMBS ---
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Tableau de bord',
    href: dashboard().url,
  }, {
    title: 'Fournisseurs',
    href: index().url,
  },
  {
    title: isEdit ? 'Modifier fournisseur' : 'Ajouter fournisseur',
    href: isEdit ? update(props.supplier!.id).url : create().url,
  },
];
</script>

<template>

  <Head :title="isEdit ? 'Modification fournisseur' : 'Création fournisseur'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle> {{ isEdit ? 'Modification fournisseur' : 'Création fournisseur' }}</CardTitle>
          <CardDescription>Saisissez les coordonnées de l'entreprise et les informations de contact du fournisseur.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

              <!-- Name Input -->
              <div class="grid gap-2">
                <Label for="name">Nom de l'entreprise</Label>
                <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="organization" name="name"
                  placeholder="Acme Supply Corp." v-model="form.name" :disabled="form.processing" />
                <InputError :message="form.errors.name" />
              </div>

              <!-- Category Input -->
              <div class="grid gap-2">
                <Label for="category">Catégorie</Label>
                <Input id="category" type="text" required :tabindex="2" autocomplete="off" name="category"
                  v-model="form.category" :disabled="form.processing" />
                <InputError :message="form.errors.category" />
              </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Email Input -->
              <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input id="email" type="email" :tabindex="3" autocomplete="email" name="email"
                  placeholder="contact@acme.com" v-model="form.email" :disabled="form.processing" />
                <InputError :message="form.errors.email" />
              </div>

              <!-- Phone Input -->
              <div class="grid gap-2">
                <Label for="phone">Tel</Label>
                <Input id="phone" type="tel" :tabindex="4" autocomplete="tel" name="phone" placeholder="+1 555-1234"
                  v-model="form.phone" :disabled="form.processing" />
                <InputError :message="form.errors.phone" />
              </div>
            </div>

            <!-- Address Input (Full width) -->
            <div class="grid gap-2">
              <Label for="address">Adresse</Label>
              <Input id="address" type="text" :tabindex="5" autocomplete="address-line1" name="address"
                placeholder="123 Industrial Park, City, Country" v-model="form.address" :disabled="form.processing" />
              <InputError :message="form.errors.address" />
            </div>

            <!-- Contact Input -->
            <div class="grid gap-2">
              <Label for="contact">Contact</Label>
              <Input id="contact" type="text" required :tabindex="6" autocomplete="off" name="contact"
                v-model="form.contact" :disabled="form.processing" />
              <InputError :message="form.errors.contact" />
            </div>

            <Button type="submit" class="mt-2 w-full" :tabindex="7" :disabled="form.processing">
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
              {{ isEdit ? 'Enregistrer' : 'Enregistrer' }}
            </Button>
          </form>
        </CardContent>
      </Card>

    </div>
  </AppLayout>
</template>
