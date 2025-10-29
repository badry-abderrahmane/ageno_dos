<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Form } from '@inertiajs/vue3';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { LucideIcon } from 'lucide-vue-next';

defineProps<{
  binded: any,
  resource: string,
  icon?: LucideIcon
}>()
</script>

<template>
  <Dialog>
    <DialogTrigger as-child>
      <Button variant="destructive">
        <template v-if="icon">
          <component :is="icon"></component>
        </template>
        <template v-else>
          Delete {{ resource }}
        </template>
      </Button>
    </DialogTrigger>
    <DialogContent>
      <Form v-bind="binded" reset-on-success :options="{
        preserveScroll: true,
      }" class="space-y-6" v-slot="{ errors, processing, reset, clearErrors }">
        <DialogHeader class="space-y-3">
          <DialogTitle>Are you sure you want to delete this {{ resource }}?</DialogTitle>
          <DialogDescription>
            Once {{ resource }} is deleted, all of its data will also be permanently
            deleted.
            <InputError v-if="errors" :message="errors.password" />
          </DialogDescription>
        </DialogHeader>

        <DialogFooter class="gap-2">
          <DialogClose as-child>
            <Button variant="secondary" @click="
              () => {
                clearErrors();
                reset();
              }
            ">
              Cancel
            </Button>
          </DialogClose>

          <Button type="submit" variant="destructive" :disabled="processing" data-test="confirm-delete-user-button">
            Delete {{ resource }}
          </Button>
        </DialogFooter>
      </Form>
    </DialogContent>
  </Dialog>
</template>