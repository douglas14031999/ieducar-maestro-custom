<template>
  <div>
    <skeleton-card-process />
    <skeleton-form-list
      v-for="i in 3"
      :key="i + '-skeleton-page-process-check'"
      :columns="1"
      :items="6"
      class="mt-5"
    />
  </div>
</template>

<script setup lang="ts">
import SkeletonCardProcess from '@/components/loaders/components/CardProcess.vue';
import SkeletonFormList from '@/components/loaders/components/FormList.vue';
</script>
