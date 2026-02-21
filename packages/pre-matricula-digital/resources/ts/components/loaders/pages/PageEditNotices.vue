<template>
  <div>
    <div class="row mt-5">
      <div class="col-12 col-lg-10 offset-lg-1">
        <!-- <x-skeleton width="20%" height="40px" /> -->
        <h2 class="title-find-school">Avisos</h2>
        <div class="mt-4">
          <x-skeleton type="text" />
          <x-skeleton type="text" />
          <x-skeleton type="text" width="60%" />
        </div>
        <div class="mt-4">
          <x-skeleton height="260px" />
        </div>
      </div>
      <div class="col-12 col-lg-10 offset-lg-1 mt-5">
        <div class="d-flex flex-row-reverse">
          <x-skeleton height="50px" width="155px" type="btn" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import XSkeleton from '@/components/loaders/XSkeleton.vue';
</script>
