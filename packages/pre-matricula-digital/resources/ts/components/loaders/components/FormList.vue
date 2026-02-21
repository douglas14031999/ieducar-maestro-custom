<template>
  <div class="row">
    <div
      v-for="c in columns"
      :key="c + '-form-list-columns'"
      class="col-12"
      :class="{ 'col-md-6': columns > 1 }"
    >
      <p class="border-bottom pb-3">
        <x-skeleton type="text" width="50%" />
      </p>
      <div class="row">
        <div
          v-for="i in items"
          :key="i + '-form-list-columns-items'"
          class="col-12 mb-3"
          :class="{ 'col-md-6': columns > 1 || items !== 2 }"
        >
          <x-skeleton height="50px" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import XSkeleton from '@/components/loaders/XSkeleton.vue';

withDefaults(
  defineProps<{
    columns?: number;
    items?: number;
  }>(),
  {
    columns: 2,
    items: 10,
  }
);
</script>
