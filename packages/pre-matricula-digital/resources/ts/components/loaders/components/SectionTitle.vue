<template>
  <div class="d-flex justify-content-between mt-5 mb-3">
    <x-skeleton :width="titleWidth" height="30px" />
    <div class="d-flex justify-content-end">
      <x-skeleton
        v-for="i in quantityButton"
        :key="i + '-section-title-qtd-btn'"
        type="btn"
        height="45px"
        width="70px"
        :class="{ 'mr-3': i < quantityButton }"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import XSkeleton from '@/components/loaders/XSkeleton.vue';

withDefaults(
  defineProps<{
    quantityButton?: number;
    titleWidth?: string;
  }>(),
  {
    quantityButton: 1,
    titleWidth: '30%',
  }
);
</script>
