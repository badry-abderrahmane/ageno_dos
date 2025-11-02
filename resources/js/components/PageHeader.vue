<script lang="ts" setup>
import { LucideIcon } from 'lucide-vue-next';

defineProps<{
  title: string,
  icon?: LucideIcon
}>()
</script>

<template>
  <div class="flex items-center border-b pb-2">
    <component :is="icon" :size="35"></component>
    <h3 class="ml-2 scroll-m-20  text-2xl font-semibold tracking-tight transition-colors first:mt-0">
      {{ title }}
    </h3>
  </div>
</template>