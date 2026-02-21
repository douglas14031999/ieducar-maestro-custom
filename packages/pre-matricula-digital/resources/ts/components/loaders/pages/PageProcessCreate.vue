<template>
  <div>
    <skeleton-form process />
    <skeleton-form-list
      v-for="i in 3"
      :key="i + '-skeleton-page-process-create'"
      :columns="1"
      :items="6"
      class="mt-5"
    />
  </div>
</template>

<script setup lang="ts">
import SkeletonForm from '@/components/loaders/components/Form.vue';
import SkeletonFormList from '@/components/loaders/components/FormList.vue';
</script>
