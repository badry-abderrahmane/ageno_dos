<script setup lang="ts">
import { Flame } from 'lucide-vue-next';
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
    <Flame class="size-5 fill-current text-white dark:text-black"></Flame>
</template>
