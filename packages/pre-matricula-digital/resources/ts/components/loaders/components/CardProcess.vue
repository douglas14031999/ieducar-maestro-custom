<template>
  <div v-if="!noTitle" class="d-flex align-items-center mb-3">
    <x-skeleton width="80%" class="mr-3" />
    <x-skeleton width="20%" />
  </div>
  <!-- eslint-disable-next-line vue/no-multiple-template-root -->
  <x-card
    v-for="j in quantity"
    :key="j + '-skeleton-card-process-item'"
    bordered
    hoverable
    class="mb-3"
  >
    <x-card-section>
      <div class="d-flex justify-content-between">
        <x-skeleton width="50%" class="mr-3" />
        <x-skeleton width="25%" />
      </div>
      <div class="mt-2 d-flex align-items-center">
        <x-skeleton type="text" width="100%" />
      </div>
      <div v-if="detailed" class="d-flex justify-content-start mt-3">
        <x-skeleton type="btn" class="mr-2" width="100px" />
        <x-skeleton type="btn" width="100px" class="mr-2" />
        <x-skeleton type="btn" width="100px" />
      </div>
      <div v-if="detailed" class="d-flex justify-content-start mt-3">
        <x-skeleton type="text" width="100%" />
      </div>
      <div v-if="detailed" class="mt-3">
        <x-skeleton type="text" width="100%" />
        <x-skeleton type="text" width="100%" />
        <x-skeleton type="text" width="50%" />
      </div>
    </x-card-section>
  </x-card>
</template>

<script setup lang="ts">
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XSkeleton from '@/components/loaders/XSkeleton.vue';

withDefaults(
  defineProps<{
    noTitle?: boolean;
    detailed?: boolean;
    quantity?: number;
  }>(),
  {
    noTitle: false,
    detailed: false,
    quantity: 2,
  }
);
</script>
