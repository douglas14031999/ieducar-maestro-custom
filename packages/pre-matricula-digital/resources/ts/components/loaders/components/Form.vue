<template>
  <div v-if="!process && !stages" class="row">
    <div
      v-for="i in 6"
      :key="i + '-skeleton-form'"
      class="col-12 col-md-6 mb-3"
    >
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" />
    </div>
    <div class="col-12">
      <div class="row">
        <div class="col-12 col-md-6">
          <x-skeleton type="text" width="40%" />
          <x-skeleton type="input" />
        </div>
      </div>
    </div>
    <div
      v-for="i in 3"
      :key="i + '-skeleton-form'"
      class="col-12 col-md-6 mb-3 mt-3"
    >
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" />
    </div>
  </div>
  <div v-else-if="process" class="row">
    <div
      v-for="i in 2"
      :key="i + '-skeleton-form'"
      :class="{
        'col-12 col-md-8 mb-3': i === 1,
        'col-12 col-md-4 mb-3': i === 2,
      }"
    >
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" />
    </div>
    <div class="col-12 mt-3">
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" width="100%" height="260px" />
    </div>
    <div class="col-12 mt-3">
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" width="100%" />
    </div>
    <div class="col-12 mt-3">
      <skeleton-text />
    </div>
  </div>
  <div v-else-if="stages" class="row">
    <div
      v-for="i in 2"
      :key="i + '-skeleton-form'"
      :class="{
        'col-12 col-md-8 mb-3': i === 1,
        'col-12 col-md-4 mb-3': i === 2,
      }"
    >
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" />
    </div>
    <div class="col-12 mt-3">
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" width="100%" />
    </div>
    <div
      v-for="i in 4"
      :key="i + '-skeleton-form-dates'"
      class="col-12 col-md-6 mb-3 mt-3"
    >
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" />
    </div>
    <div class="col-12 mt-3">
      <x-skeleton type="text" width="40%" />
      <x-skeleton type="input" width="100%" height="260px" />
    </div>
    <div class="col-12 mt-3">
      <skeleton-text />
    </div>
  </div>
</template>

<script setup lang="ts">
import SkeletonText from '@/components/loaders/components/Text.vue';
import XSkeleton from '@/components/loaders/XSkeleton.vue';

withDefaults(
  defineProps<{
    process?: boolean;
    stages?: boolean;
  }>(),
  {
    process: false,
    stages: false,
  }
);
</script>
