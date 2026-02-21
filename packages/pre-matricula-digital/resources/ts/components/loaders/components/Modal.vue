<template>
  <x-card-section>
    <div class="d-flex justify-content-between">
      <div class="flex-grow-1 modal-title">
        <div class="d-flex justify-content-start">
          <x-skeleton width="60%" class="m-2" />
        </div>
      </div>
      <div class="d-flex align-items-center">
        <x-skeleton type="circle" width="36px" height="36px" />
      </div>
    </div>
  </x-card-section>

  <hr class="m-0" />

  <x-card-section
    class="pt-2 pb-2"
    style="max-height: calc(100vh - 360px); overflow-y: auto"
    :class="bodyClass"
  >
    <div v-if="loadingInputs">
      <skeleton-form />
    </div>
    <div v-else-if="loadingText" class="container">
      <div class="row">
        <div
          v-for="i in 16"
          :key="i + '-skeleton-modal-text'"
          class="col-6 mt-4"
        >
          <skeleton-text />
        </div>
      </div>
    </div>
    <div v-else>Carregando...</div>
  </x-card-section>

  <hr v-if="!noFooter" class="m-0" />

  <x-card-section v-if="!noFooter">
    <div class="d-flex justify-content-center">
      <x-skeleton type="btn" class="mr-3" />
      <x-skeleton type="btn" />
    </div>
  </x-card-section>
</template>

<script setup lang="ts">
import SkeletonForm from '@/components/loaders/components/Form.vue';
import SkeletonText from '@/components/loaders/components/Text.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XSkeleton from '@/components/loaders/XSkeleton.vue';

withDefaults(
  defineProps<{
    noFooter: boolean;
    loadingInputs: boolean;
    loadingText: boolean;
    bodyClass: string;
  }>(),
  {
    noFooter: false,
    loadingInputs: false,
    loadingText: false,
    bodyClass: '',
  }
);
</script>
