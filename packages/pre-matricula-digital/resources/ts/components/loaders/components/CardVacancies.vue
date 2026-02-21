<template>
  <div class="row">
    <div
      v-for="i in 4"
      :key="i + '-skeleton-card-vacancies'"
      class="col-12 col-md-6 mt-4"
    >
      <x-card bordered>
        <x-card-section>
          <div class="d-flex justify-content-between mb-4">
            <x-skeleton class="w-60" />
            <x-skeleton class="w-30" />
          </div>
          <div class="d-flex justify-content-center">
            <x-skeleton height="300px" width="300px" type="circle" />
          </div>
          <div class="row p-3">
            <div class="col-12 px-3">
              <div class="d-flex justify-content-between">
                <div class="w-75">
                  <x-skeleton width="60%" />
                  <x-skeleton width="60%" type="text" />
                </div>
                <div class="w-25">
                  <x-skeleton type="text" />
                  <x-skeleton type="text" />
                </div>
              </div>
              <div
                class="mt-2"
                style="margin-left: -0.25rem; margin-right: -0.25rem"
              >
                <x-skeleton class="w-100" />
                <x-skeleton class="w-100" type="text" />
                <x-skeleton class="w-100" height="5px" />
              </div>
              <div
                class="mt-4"
                style="margin-left: -0.25rem; margin-right: -0.25rem"
              >
                <x-skeleton class="w-100" />
                <x-skeleton class="w-100" type="text" />
                <x-skeleton class="w-100" height="5px" />
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-12">
              <x-skeleton type="button" height="50px" />
            </div>
          </div>
        </x-card-section>
      </x-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XSkeleton from '@/components/loaders/XSkeleton.vue';
</script>
