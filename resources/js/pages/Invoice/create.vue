<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { create, store, update } from '@/routes/invoice';
import { dashboard } from '@/routes';
import { Client, Invoice, type BreadcrumbItem } from '@/types';
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

import { toast } from 'vue-sonner'

const props = defineProps<{
  invoice?: Invoice,
  clients: Client[]
}>()

const isEdit = !!props.invoice;
const form = useForm({
  client_id: props.invoice?.client_id ?? undefined,
  total: props.invoice?.total ?? 0,
  status: props.invoice?.status ?? 'not_paid',
});

const submit = () => {
  if (isEdit) {
    form.patch(update.url(props.invoice!.id), {
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
    title: isEdit ? 'Edit invoice' : 'Create invoice',
    href: isEdit ? update.url(props.invoice!.id) : create().url,
  },
];
</script>

<template>

  <Head :title="isEdit ? 'Edit invoice' : 'Create invoice'" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle> {{ isEdit ? 'Edit invoice' : 'Create invoice' }}</CardTitle>
          <CardDescription>Products and prices</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">

              <!-- Client Select -->
              <div class="grid gap-2">
                <Label for="client_id">Client</Label>
                <Select id="client_id" name="client_id" v-model:model-value="form.client_id"
                  :disabled="form.processing">
                  <SelectTrigger>
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

              <!-- Total Input -->
              <div class="grid gap-2">
                <Label for="total">Total</Label>
                <Input id="total" type="text" required autofocus :tabindex="1" autocomplete="total" name="total"
                  placeholder="Invoice total" v-model="form.total" :disabled="form.processing" />
                <InputError :message="form.errors.total" />
              </div>

              <!-- Status Select -->
              <div class="grid gap-2">
                <Label for="status">Status</Label>
                <Select id="status" required name="status" v-model:model-value="form.status"
                  :disabled="form.processing">
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem value="not_paid">
                        Not Paid
                      </SelectItem>
                      <SelectItem value="paid">
                        Paid
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                <InputError :message="form.errors.status" />
              </div>

              <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                {{ isEdit ? 'Update Invoice' : 'Create Invoice' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>

    </div>
  </AppLayout>
</template>
