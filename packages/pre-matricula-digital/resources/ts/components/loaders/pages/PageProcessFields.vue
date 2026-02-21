<template>
  <div>
    <skeleton-form-list v-for="i in 2" :key="i + 'process'" :columns="1" />
  </div>
</template>

<script setup lang="ts">
import SkeletonFormList from '@/components/loaders/components/FormList.vue';
</script>
