<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div data-slot="card" :class="cn(
    'bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm animate-in fade-in slide-in-from-top-12 duration-500 fill-mode-both',
    props.class,
  )
    ">
    <slot />
  </div>
</template>
