<template>
  <div class="row mt-4">
    <div class="col-12 col-md-10 offset-md-1">
      <x-card bordered>
        <x-card-section class="pl-4 pr-4 pt-3 p-0">
          <h5 class="card-title w-30">
            <x-skeleton type="rect" />
          </h5>
        </x-card-section>
        <x-card-section class="pl-4 pr-4 pb-3 p-0">
          <div class="row">
            <div class="col-12">
              <x-skeleton type="text" class="w-100" />
              <x-skeleton type="text" class="w-100" />
              <x-skeleton type="text" class="w-100" />
              <x-skeleton type="text" class="w-50" />
            </div>
          </div>
          <div class="row">
            <div
              class="col-11 col-lg-2 font-hind offset-lg-10 pr-0 pr-md-4 text-right"
            >
              <x-skeleton type="text" />
            </div>
          </div>
        </x-card-section>
      </x-card>
    </div>
  </div>
</template>

<script lang="ts">
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XSkeleton from '@/components/loaders/XSkeleton.vue';
import { defineComponent } from 'vue';
export default defineComponent({
  components: {
    XSkeleton,
    XCard,
    XCardSection,
  },
});
</script>
