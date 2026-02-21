<template>
  <div>
    <x-skeleton
      v-for="i in quantity"
      :key="i + '-skeleton-text'"
      :width="i < quantity ? '100%' : '60%'"
      type="text"
    />
  </div>
</template>

<script setup lang="ts">
import XSkeleton from '@/components/loaders/XSkeleton.vue';

withDefaults(
  defineProps<{
    quantity?: number;
  }>(),
  {
    quantity: 3,
  }
);
</script>
