<template>
  <div v-if="stats" class="row mt-5 mb-5 pl-lg-0 pl-3 pr-lg-0 pr-3">
    <div
      class="col-12 p-0 d-flex justify-content-lg-around"
      style="overflow-x: auto"
    >
      <x-card
        v-for="(card, index) in statsCards"
        :key="index + '-stats-cards'"
        :class="card.cardClass"
        bordered
        class="bg-background card-summary mr-3 mr-lg-0"
      >
        <x-card-section class="d-flex justify-content-center mt-3 mb-3 pb-0">
          <div
            class="rounded-circle p-3 border-card"
            :class="card.bgColor"
          ></div>
        </x-card-section>
        <x-card-section class="text-center pt-0 pb-3">
          <p class="card-number">
            {{ card.quantity }}
          </p>
          <p class="card-text">
            {{ card.text }}
          </p>
        </x-card-section>
      </x-card>
    </div>
  </div>
  <div v-else class="row mt-5 mb-5 pl-lg-0 pl-3 pr-lg-0 pr-3">
    <div
      class="col-12 p-0 d-flex justify-content-lg-around"
      style="overflow-x: auto"
    >
      <skeleton-card-stats
        v-for="(_, index) in 5"
        :key="index + '-stats-cards-skeleton'"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import SkeletonCardStats from '@/components/loaders/components/CardStats.vue';
import { StatProcesses } from '@/modules/preregistration/types';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import { computed } from 'vue';

const props = defineProps<{
  stats?: StatProcesses;
}>();

const statsCards = computed(() => {
  if (!props.stats || Object.keys(props.stats as StatProcesses).length === 0)
    return [];

  return [
    {
      quantity: props.stats ? props.stats.vacancies : null,
      text: 'Vagas ofertadas',
      bgColor: 'bg-blue svg-real-estate',
      cardClass: 'col-6 col-md-2',
    },
    {
      quantity: props.stats ? props.stats.total : null,
      text: 'Pré-matrículas inscritas',
      bgColor: 'bg-blue svg-perfis',
      cardClass: 'col-6 col-md-2',
    },
    {
      quantity: props.stats ? props.stats.accepted : null,
      text: 'Deferidas',
      bgColor: 'bg-green svg-aprovado',
      cardClass: 'col-6 col-md-2',
    },
    {
      quantity: props.stats ? props.stats.rejected : null,
      text: 'Indeferidas',
      bgColor: 'bg-red svg-desaprovado',
      cardClass: 'col-6 col-md-2',
    },
    {
      quantity: props.stats ? props.stats.waiting : null,
      text: 'Em espera',
      bgColor: 'bg-yellow svg-esperando',
      cardClass: 'col-6 col-md-2',
    },
  ];
});
</script>
