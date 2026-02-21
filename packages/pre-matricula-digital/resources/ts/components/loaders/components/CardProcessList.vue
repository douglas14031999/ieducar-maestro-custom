<template>
  <div>
    <skeleton-section-title :quantity-button="3" />

    <div class="row">
      <div
        v-for="i in 2"
        :key="i + '-skeleton-card-process'"
        class="col-12 col-md-6 mt-4"
      >
        <skeleton-card-process :quantity="1" no-title detailed />
      </div>
    </div>

    <skeleton-section-title />
    <skeleton-form-list />

    <skeleton-section-title />
    <skeleton-form-list :columns="1" :items="4" />
  </div>
</template>

<script setup lang="ts">
import SkeletonCardProcess from '@/components/loaders/components/CardProcess.vue';
import SkeletonFormList from '@/components/loaders/components/FormList.vue';
import SkeletonSectionTitle from '@/components/loaders/components/SectionTitle.vue';
</script>
