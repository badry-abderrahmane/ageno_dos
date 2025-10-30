<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { create, store, update } from '@/routes/client';
import { dashboard } from '@/routes';
import { Client, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'

import { toast } from 'vue-sonner'

defineProps<{
  client?: Client
}>()

const handleError = () => {
  toast('Error creating client')
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: 'Create client',
    href: create().url,
  },
];
</script>

<template>

  <Head title="Create client" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle> Create client</CardTitle>
          <CardDescription>Designation and contact</CardDescription>
        </CardHeader>
        <CardContent>
          <Form v-bind="client?.id ? update.form(client.id) : store.form()" :reset-on-success="['name', 'ice']"
            @error="handleError" v-slot="{ errors, processing }" class="flex flex-col gap-6">
            <div class="grid gap-6">
              <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" name="name"
                  placeholder="Client name" :model-value="client?.name" />
                <InputError :message="errors.name" />
              </div>

              <div class="grid gap-2">
                <Label for="ice">ICE</Label>
                <Input id="ice" type="number" required autofocus :tabindex="1" autocomplete="ice" name="ice"
                  placeholder="ICE" :model-value="client?.ice" />
                <InputError :message="errors.ice" />
              </div>

              <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="processing"
                data-test="register-user-button">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Save
              </Button>
            </div>
          </Form>
        </CardContent>
      </Card>

    </div>
  </AppLayout>
</template>
