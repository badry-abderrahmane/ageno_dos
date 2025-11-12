<script setup lang="ts">
import { formatNumber } from '@/lib/utils';
import { LucideIcon } from 'lucide-vue-next';
import { defineProps } from 'vue';

interface Props {
  title: string;
  // IMPORTANT: We treat `value` as `string | number` but must clean it before abbreviation
  value: string | number;
  icon?: LucideIcon;
}

defineProps<Props>();
</script>

<template>
  <div class="rounded-xl border bg-card text-card-foreground shadow">
    <div class="gap-y-1.5 p-6 flex flex-row items-center justify-between space-y-0 pb-2">
      <h3 class="tracking-tight text-sm font-normal"> {{ title }} </h3>
      <component v-if="icon" :is="icon"></component>
    </div>
    <div class="p-6 pt-0">
      <div class="text-2xl font-bold"> {{ formatNumber(value) }} </div>
      <p class="text-xs text-muted-foreground"> +20.1% from last month </p>
    </div>
  </div>
</template>