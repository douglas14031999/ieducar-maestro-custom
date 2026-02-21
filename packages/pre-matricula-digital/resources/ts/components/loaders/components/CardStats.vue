<template>
  <x-card
    bordered
    class="bg-background card-summary mr-3 mr-lg-0 mb-md-0 col-6 col-md-2"
  >
    <x-card-section class="d-flex justify-content-center mt-3 mb-3 pb-0">
      <x-skeleton type="circle" height="60px" width="60px" />
    </x-card-section>
    <x-card-section class="d-flex justify-content-center pt-0 pb-3">
      <p class="card-number">
        <x-skeleton height="58px" width="80px" />
      </p>
    </x-card-section>
    <x-card-section class="d-flex justify-content-center pt-0 pb-3">
      <p class="card-text">
        <x-skeleton type="text" width="100px" />
      </p>
    </x-card-section>
  </x-card>
</template>

<script setup lang="ts">
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XSkeleton from '@/components/loaders/XSkeleton.vue';
</script>
