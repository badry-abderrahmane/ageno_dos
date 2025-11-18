<script setup lang="ts">
import { Leaf } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <Leaf class="size-5  text-green-200 dark:text-green-900"></Leaf>
</template>
