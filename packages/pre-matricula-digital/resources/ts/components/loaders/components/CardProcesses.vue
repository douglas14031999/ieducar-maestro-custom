<template>
  <div class="row mt-3">
    <div class="container-sm col-12 col-md-10 offset-md-1 mt-4">
      <div class="row">
        <div class="col">
          <h2 class="title mb-3 w-30">
            <x-skeleton />
          </h2>
        </div>
      </div>
      <div v-for="i in 3" :key="i + '-skeleton-card-processes'">
        <div class="row">
          <div class="col-12 mt-3 mt-sm-2">
            <h4 class="m-0 w-60">
              <x-skeleton type="text" />
            </h4>
          </div>
        </div>
        <div class="row">
          <template
            v-for="j in 2"
            :key="j + '-skeleton-card-processes-stage-card'"
          >
            <div class="col-12 col-md-6 mt-3 mt-sm-2">
              <x-card hoverable bordered class="mb-3">
                <x-card-section>
                  <div class="d-flex justify-content-between">
                    <div class="pl-3 pl-lg-4 w-40">
                      <x-skeleton />
                    </div>
                    <div class="pr-3 pr-lg-4 w-40">
                      <x-skeleton />
                    </div>
                  </div>
                  <div class="mt-0 mt-lg-2">
                    <div class="d-flex flex-column flex-lg-row">
                      <div class="pb-2 pb-lg-0 pl-4 pl-lg-4 w-30 mr-3">
                        <x-skeleton type="text" />
                      </div>
                      <div
                        class="d-flex justify-content-between flex-grow-1 pl-4 pl-lg-0"
                      >
                        <div class="w-100">
                          <x-skeleton type="text" />
                        </div>
                        <div class="pr-4 w-40 ml-3">
                          <x-skeleton type="text" />
                        </div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-center">
                      <div class="flex-grow-1 pl-3 pl-lg-4 pr-3 pr-lg-4">
                        <hr class="mb-2 mt-2" />
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-lg-row">
                      <div class="pb-2 pb-lg-0 pl-4 pl-lg-4 w-30 mr-3">
                        <x-skeleton type="text" />
                      </div>
                      <div
                        class="d-flex justify-content-between flex-grow-1 pl-4 pl-lg-0"
                      >
                        <div class="w-100">
                          <x-skeleton type="text" />
                        </div>
                        <div class="pr-4 w-40 ml-3">
                          <x-skeleton type="text" />
                        </div>
                      </div>
                    </div>
                  </div>
                </x-card-section>
              </x-card>
            </div>
          </template>
        </div>
      </div>
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
