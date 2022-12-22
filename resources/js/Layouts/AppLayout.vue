<template>
  <Head :title="title" />
  <slot />
</template>

<script setup>
import { Head } from '@inertiajs/inertia-vue3';

defineProps({
  title: String,
});
</script>
