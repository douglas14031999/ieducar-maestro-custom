<template>
  <x-card
    bordered
    :class="{ 'card-process-disabled': stageCardDisabled(stage.status) }"
    hoverable
  >
    <x-card-section class="pb-0">
      <div class="d-flex justify-content-between">
        <div class="pl-3 pl-lg-4">
          <h5 class="text-h5">
            {{ stageTypeText(stage.type) }}
          </h5>
        </div>
        <div class="pr-3 pr-lg-4">
          <small
            :class="stageStatusBadge(stage.status)"
            class="badge pull-right text-wrap"
          >
            {{ stageStatusText(stage.status) }}
          </small>
        </div>
      </div>
    </x-card-section>

    <x-card-section class="pb-0">
      <div class="d-flex flex-column flex-lg-row">
        <div class="pb-2 pb-lg-0 pl-4 pl-lg-4">
          <span class="text-gray-600 mr-4"> Início </span>
        </div>
        <div class="d-flex justify-content-between flex-grow-1 pl-4 pl-lg-0">
          <div class="flex-lg-grow-1 text-lg-center text-size-base">
            <i class="fa fa-calendar text-primary mr-2"></i>
            <span class="text-gray-600">{{
              $filters.formatDate(stage.startAt)
            }}</span>
          </div>
          <div class="pr-4 text-size-base">
            <i class="fa fa-clock-o text-primary mr-2"></i>
            <span class="text-gray-600">{{
              $filters.formatTime(stage.startAt)
            }}</span>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-center">
        <div class="flex-grow-1 pl-3 pl-lg-4 pr-3 pr-lg-4">
          <hr class="mb-2 mt-2" />
        </div>
      </div>
      <div class="d-flex flex-column flex-lg-row">
        <div class="pb-2 pb-lg-0 pl-4">
          <span class="text-gray-600"> Término </span>
        </div>
        <div class="d-flex justify-content-between flex-grow-1 pl-4 pl-lg-0">
          <div class="flex-lg-grow-1 text-lg-center text-size-base">
            <i class="fa fa-calendar text-primary mr-2"></i>
            <span class="text-gray-600">{{
              $filters.formatDate(stage.endAt)
            }}</span>
          </div>
          <div class="pr-4 text-size-base">
            <i class="fa fa-clock-o text-primary mr-2"></i>
            <span class="text-gray-600">{{
              $filters.formatTime(stage.endAt)
            }}</span>
          </div>
        </div>
      </div>
    </x-card-section>

    <x-card-section class="pt-0">
      <x-btn
        data-test="btn-process-stage-card"
        :disable="stageCardDisabled(stage.status)"
        class="mt-4 w-100"
        no-caps
        no-wrap
        color="primary"
        size="lg"
        label="Fazer inscrição"
        @click="registration"
      />
    </x-card-section>
  </x-card>
</template>

<script setup lang="ts">
import {
  stageCardDisabled,
  stageStatusBadge,
  stageStatusText,
  stageTypeText,
} from '@/util';
import { Filters } from '@/filters';
import { Stages } from '@/types';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import { inject } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const $filters = inject('$filters') as Filters;

const props = defineProps<{
  stage: Stages;
}>();

const registration = () => {
  router.push({
    name: 'preregistration',
    params: {
      id: props.stage.id,
    },
  });
};
</script>
