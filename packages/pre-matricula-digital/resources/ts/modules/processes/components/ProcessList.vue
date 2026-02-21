<template>
  <main>
    <div class="d-flex justify-content-between">
      <h1>Processos</h1>
      <x-btn
        data-test="new-process-button"
        label="Criar novo processo"
        color="primary"
        no-caps
        no-wrap
        class="flex-row"
        @click="pushRouter"
      />
    </div>
    <div class="mb-4 mt-4">
      <x-card flat class="bg-primary">
        <x-card-section class="pl-2 pr-2">
          <div class="row m-0 filter-gutters">
            <x-field
              v-model="filter.year"
              container-class="col-12 col-md mb-md-0"
              name="year"
              type="SELECT"
              :options="years"
              :allow-clear="true"
              placeholder="Filtrar ano"
            />
            <x-field
              v-model="filter.id"
              container-class="col-12 col-md-6 mb-md-0"
              name="id"
              type="SELECT"
              :options="filteredProcesses"
              :allow-clear="true"
              placeholder="Filtrar por nome do processo"
              searchable
            />
            <x-field
              v-model="filter.status"
              container-class="col-12 col-md mb-md-0"
              name="status"
              type="SELECT"
              :options="status"
              :allow-clear="true"
              placeholder="Filtrar por situação"
            />
          </div>
        </x-card-section>
      </x-card>
    </div>
    <div v-if="loading" class="row">
      <div
        v-for="i in 6"
        :key="i + '-skeleton-card-process'"
        class="col-12 col-md-6 mt-4"
      >
        <card-process />
      </div>
    </div>
    <div v-else class="row">
      <div
        v-for="process in filteredProcesses"
        :key="process.id"
        data-test="process-block"
        class="col-12 col-md-6 mt-4"
      >
        <div class="d-flex justify-content-between">
          <router-link
            v-tooltip.bottom-start="
              `${process.name} (${process.schoolYear.year})`
            "
            :to="{ name: 'process.show', params: { id: process.id } }"
            class="text-truncate h4 text-blue-dark"
            :class="{ 'text-muted': !process.active }"
          >
            {{ process.name }} ({{ process.schoolYear.year }})
          </router-link>
          <div v-if="!process.active" class="d-flex align-items-start mt-n1">
            <span class="badge badge-gray"> Inativo </span>
          </div>
        </div>
        <x-card
          v-for="stage in process.stages"
          :key="stage.id"
          data-test="stage-card"
          bordered
          :class="{ 'card-process-disabled': stageCardDisabled(stage.status) }"
          hoverable
          class="mb-3"
        >
          <x-card-section class="pb-0">
            <h5 class="text-h5">
              {{ stageTypeText(stage.type) }}
              <small
                :class="stageStatusBadge(stage.status)"
                class="badge pull-right"
              >
                {{ stageStatusText(stage.status) }}
              </small>
            </h5>
          </x-card-section>
          <x-card-section class="pt-0">
            <div class="mt-2 d-flex align-items-center text-size-15">
              <i class="fa fa-calendar text-primary mr-2"></i>
              <span class="text-gray-600">
                {{ $filters.formatDateTime(stage.startAt) }} à
                {{ $filters.formatDateTime(stage.endAt) }}
              </span>
            </div>
          </x-card-section>
        </x-card>
        <x-card v-if="process.stages.length === 0" bordered>
          <x-card-section class="text-muted p-3">
            Nenhum período foi encontrado.
          </x-card-section>
        </x-card>
      </div>
      <x-card v-if="filteredProcesses.length === 0" class="col-12" bordered>
        <x-card-section class="text-muted p-3">
          Nenhum processo foi encontrado.
        </x-card-section>
      </x-card>
    </div>
  </main>
</template>

<script setup lang="ts">
import { Process, ProcessGetList } from '@/modules/processes/types';
import { computed, inject, ref } from 'vue';
import {
  stageCardDisabled,
  stageStatusBadge,
  stageStatusText,
  stageTypeText,
} from '@/util';
import CardProcess from '@/components/loaders/components/CardProcess.vue';
import { Filters } from '@/filters';
import { Option } from '@/types';
import { Process as ProcessApi } from '@/modules/processes/api';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XField from '@/components/x-form/XField.vue';
import { useLoader } from '@/composables';
import { useRouter } from 'vue-router';

const $filters = inject('$filters') as Filters;

const { loader, loading } = useLoader<ProcessGetList>();

const router = useRouter();

const processes = ref<Process[]>([]);
const filter = ref({
  year: null,
  status: null,
  id: null,
});
const years = ref<Option[]>([]);
const status = ref<Option[]>([]);

const filteredProcesses = computed(() => {
  let filteredProcesses = processes.value;

  if (filter.value.id) {
    const text = (filter.value.id as string).slugify();

    filteredProcesses = filteredProcesses.filter(
      (process) =>
        process.id.includes(text) || process.name.slugify().includes(text)
    );
  }

  if (filter.value.year) {
    filteredProcesses = filteredProcesses.filter(
      (process) => process.schoolYear.year === filter.value.year
    );
  }

  if (filter.value.status) {
    filteredProcesses = filteredProcesses.filter((process) =>
      process.stages.find((stage) => stage.status === filter.value.status)
    );
    filteredProcesses = filteredProcesses.map((process) => {
      const internalProcess = { ...process };
      internalProcess.stages = internalProcess.stages.filter(
        (stage) => stage.status === filter.value.status
      );
      return internalProcess;
    });
  }
  return filteredProcesses;
});

const getData = () => {
  loader(() => ProcessApi.getList()).then((res) => {
    processes.value = res.processes;
    years.value = res.years;
    status.value = res.status;
  });
};

const pushRouter = () => {
  router.push({ name: 'process.create', params: { newProcess: 'true' } });
};

getData();
</script>
