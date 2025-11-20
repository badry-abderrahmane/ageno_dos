<script setup lang="ts">
import { formatNumber } from '@/lib/utils';
import { StatWithComparison } from '@/types';
import { LucideIcon } from 'lucide-vue-next';
import { defineProps } from 'vue';

interface Props {
  title: string;
  stat: StatWithComparison<string | number>;
  icon?: LucideIcon;
}

defineProps<Props>();
</script>

<template>
  <div
    class="animate-in fade-in slide-in-from-top-12 duration-500 fill-mode-both rounded-xl border bg-card text-card-foreground shadow">
    <div class="gap-y-1.5 p-6 flex flex-row items-center justify-between space-y-0 pb-2">
      <h3 class="tracking-tight text-sm font-normal"> {{ title }} </h3>
      <component v-if="icon" :is="icon" class="size-10 text-muted-foreground/70"></component>
    </div>
    <div class="p-6 pt-0">
      <div class="text-2xl font-bold"> {{ formatNumber(stat.value) }} </div>
      <p v-if="stat?.comparison" :class="stat?.comparison.includes('+') ? 'text-green-400/70' : 'text-red-400/70'"
        class="text-xs "> {{ stat.comparison }} </p>
    </div>
  </div>
</template>