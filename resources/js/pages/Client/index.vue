<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create, edit, destroy } from '@/routes/client';
import { Client, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import DeleteConfirm from '@/components/DeleteConfirm.vue'
import { defineProps } from 'vue';
import { Pencil, Trash } from 'lucide-vue-next';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import Button from '@/components/ui/button/Button.vue';

defineProps<{
  clients: Client[]
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: index().url,
  },
];
</script>

<template>

  <Head title="Clients" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <h3 class="mt-10 scroll-m-20 border-b pb-2 text-2xl font-semibold tracking-tight transition-colors first:mt-0">
        Clients list
      </h3>
      <Button class="max-w-40" @click="router.visit(create())">Add new</Button>
      <div
        class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>
                Name
              </TableHead>
              <TableHead>Ice</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="client in clients" :key="client.id">
              <TableCell class="font-medium">
                {{ client.name }}
              </TableCell>
              <TableCell>{{ client.ice }}</TableCell>
              <TableCell>
                <Button class="mr-1" @click="router.visit(edit(client.id).url)">
                  <Pencil />
                </Button>
                <DeleteConfirm :binded="destroy.form(client.id)" resource="client" :icon="Trash">
                </DeleteConfirm>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
