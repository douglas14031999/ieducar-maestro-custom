<template>
  <div class="container" style="max-width: 740px">
    <skeleton-section-title :quantity-button="0" title-width="70%" />
    <skeleton-text :quantity="3" />
    <hr class="mt-5" />
    <skeleton-section-title :quantity-button="1" title-width="50%" />
    <skeleton-form />
    <skeleton-text :quantity="3" />
    <skeleton-form class="mt-3" />
  </div>
</template>

<script setup lang="ts">
import SkeletonForm from '@/components/loaders/components/Form.vue';
import SkeletonSectionTitle from '@/components/loaders/components/SectionTitle.vue';
import SkeletonText from '@/components/loaders/components/Text.vue';
</script>
