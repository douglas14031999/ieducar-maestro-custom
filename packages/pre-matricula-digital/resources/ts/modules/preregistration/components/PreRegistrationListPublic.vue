<template>
  <main>
    <h1>Consulta da lista de espera</h1>
    <x-card class="bg-primary mt-4 mb-3">
      <x-card-section class="pl-2 pr-2">
        <form @submit.prevent="load(1)">
          <div class="row m-0 filter-gutters">
            <x-field
              v-model="filter.school"
              container-class="col-12 col-md-3 mb-2 mb-md-0"
              name="school"
              placeholder="Filtrar por escola"
              type="SELECT"
              :options="schools"
              searchable
              @change="filter.canAcceptInBatch = false"
            />
            <x-field
              v-model="filter.grade"
              container-class="col-12 col-md-3 mb-2 mb-md-0"
              name="grade"
              placeholder="Filtrar por série"
              type="SELECT"
              :options="grades"
              searchable
              @change="filter.canAcceptInBatch = false"
            />
            <x-field
              v-model="filter.period"
              container-class="col-12 col-md-3 mb-2 mb-md-0"
              name="period"
              placeholder="Filtrar por turno"
              type="SELECT"
              :options="getPeriods"
            />
            <x-field
              v-model="filter.status"
              container-class="col-12 col-md-3 mb-2 mb-md-0"
              name="status"
              placeholder="Filtrar por status"
              type="SELECT"
              :options="getStatuses"
            />
          </div>
          <div class="d-flex m-0 mt-3 filter-gutters flex-column flex-lg-row">
            <p v-if="paginator" class="text-white pt-2 pl-2">
              Total de alunos(as) em espera: {{ paginator?.total }}
            </p>
            <div class="d-flex justify-content-end flex-grow-1 mt-2 mt-md-0">
              <x-btn
                data-test="cleanFilter"
                label="Limpar"
                color="white"
                flat
                class="mr-3"
                no-caps
                no-wrap
                @click="cleanFilter"
              />
              <x-btn
                data-test="btn-search-pre-registration"
                label="Buscar"
                color="white"
                outline
                class="mr-2"
                no-caps
                no-wrap
                :loading="loadingTable"
                loading-normal
                @click="load(1)"
              />
            </div>
          </div>
        </form>
      </x-card-section>
    </x-card>

    <skeleton-table v-if="loadingTable" />

    <table
      v-else-if="!loadingTable && alreadyLoaded"
      class="table table-responsive table-striped table-vcenter"
    >
      <thead>
        <tr>
          <th>Status</th>
          <th>Posição</th>
          <th>Iniciais do(a) aluno(a)</th>
          <th>Escola</th>
          <th>Série</th>
          <th>Turno</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="noData">
          <td colspan="9">Nenhum resultado.</td>
        </tr>
        <tr
          v-for="preregistration in preregistrations"
          v-else
          :key="preregistration.id"
        >
          <td>
            <span
              v-tooltip.bottom-start="
                preRegistrationStatusText(preregistration.status)
              "
              :class="preRegistrationStatusClass(preregistration.status)"
            ></span>
          </td>
          <td>{{ preregistration.position }}º</td>
          <td>
            {{ preregistration.student?.initials.toUpperCase() }}
          </td>
          <td>
            {{ preregistration.school.name }}
          </td>
          <td>
            {{ preregistration.grade ? preregistration.grade.name : '-' }}
          </td>
          <td>
            {{ preregistration.period ? preregistration.period.name : '-' }}
          </td>
        </tr>
      </tbody>
    </table>
    <div v-else class="text-center mt-5">
      <p>Para ter acesso a lista de espera, utilize os filtros acima.</p>
      <p>
        Recomendamos o uso dos campos Escola, Série, para uma melhor
        visualização.
      </p>
    </div>
    <pagination :paginator-info="paginator" @page="load($event as number)" />

    <div class="row">
      <div v-if="priorityFields?.length === 0" class="col-12 text-muted">
        Nenhum critério de prioridade foi definido.
      </div>
    </div>
    <div v-if="priorityFields" class="row">
      <div class="col-12">
        <h1 class="mt-5 text-center" style="font-size: 26px">
          Critérios de priorização da lista de espera
        </h1>
      </div>
      <div class="col-2"></div>
      <div class="col-8">
        <div class="row mt-2 text-justify" v-html="criteria"></div>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
/* eslint-disable sort-imports */
import { computed, ref } from 'vue';
import {
  Filter,
  GetProcess,
  Grade,
  Period,
  PreRegistrationList,
  PreregistrationLoad,
  ProcessField,
  School,
} from '../types';
import { Option, PaginatorInfo } from '@/types';
import { Preregistration as PreregistrationApi } from '@/modules/preregistration/api';
import { preRegistrationStatusClass, preRegistrationStatusText } from '@/util';
import { useLoader } from '@/composables';
import Pagination from '@/components/resource/Pagination.vue';
import SkeletonTable from '@/components/loaders/components/Table.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XField from '@/components/x-form/XField.vue';
import { useRoute } from 'vue-router';

const { loader } = useLoader<PreregistrationLoad>();

const { loader: loaderPreregistrations } = useLoader<GetProcess>();

const alreadyLoaded = ref(false);
const route = useRoute();

const checked = ref<string[]>([]);
const paginator = ref<PaginatorInfo>();
const preregistrations = ref<PreRegistrationList[]>([]);
const newFilter = ref<Filter>({
  status: 'WAITING',
  search: '',
  process: null,
  school: null,
  grade: null,
  sort: 'POSITION',
  canAcceptInBatch: false,
  year: null,
  schools: null,
});
const filter = ref<Filter>({
  status: 'WAITING',
  search: '',
  process: null,
  school: null,
  grade: null,
  sort: 'POSITION',
  canAcceptInBatch: false,
  year: null,
  schools: null,
});
const process = ref<GetProcess>();
const criteria = ref('');
const schools = ref<School[]>([]);
const grades = ref<Grade[]>([]);
const periods = ref<Period[]>([]);
const statuses = ref(['WAITING', 'SUMMONED', 'IN_CONFIRMATION']);
const loadingTable = ref(false);

const getPeriods = computed(() => {
  const type = {
    key: null,
    label: 'Nenhum',
  };

  const types: Option[] = periods.value.map((period) => ({
    key: period.id,
    label: period.name,
  }));

  types.unshift(type);

  return types;
});

const getStatuses = computed(() => {
  const type = {
    key: null,
    label: 'Nenhum',
  };

  const types: Option[] = statuses.value.map((status) => ({
    key: status,
    label: preRegistrationStatusText(status),
  }));

  types.unshift(type);

  return types;
});

const noData = computed(() => {
  return loadingTable.value === false && preregistrations.value.length === 0;
});

const load = (page?: number) => {
  loadingTable.value = true;
  alreadyLoaded.value = true;

  const copyFilter = { ...filter.value, page };

  Object.keys(copyFilter).forEach((key) => {
    if (
      !copyFilter[key as keyof typeof copyFilter] ||
      copyFilter[key as keyof typeof copyFilter] === ''
    ) {
      delete copyFilter[key as keyof typeof copyFilter];
    }
  });

  /**
   * Realiza checagem destes campos abaixo, pois os mesmos devem
   * ser números.
   *
   * Este problema deve acontecer em alguns navegadores que não disponibiliza a
   * opção de desativar o autocomplete, como é feito na grande maioria dos browsers.
   *
   * Exemplo
   *
   * Permitido:
   *
   * grade: "5"
   *
   * Não Permitido:
   *
   * grade: "BI"
   */
  const checkNumberType = ['period', 'process', 'school', 'grade'];

  checkNumberType.forEach((key) => {
    let item = copyFilter[key as keyof typeof copyFilter];

    if (!Number(item)) {
      delete copyFilter[key as keyof typeof copyFilter];
    }
  });

  loader(() =>
    PreregistrationApi.getList({
      ...copyFilter,
      first: 50,
      process: route.params.id as string,
    })
  )
    .then((res) => {
      if (!res) return;

      preregistrations.value = res.preregistrations;

      paginator.value = res.paginator;
      checked.value = [];
      const { process, school, grade } = filter.value;
      if (process && school && grade) {
        filter.value.canAcceptInBatch = true;
      }
    })
    .finally(() => {
      loadingTable.value = false;
    });
};

const cleanFilter = () => {
  filter.value = { ...newFilter.value };
  load(1);
};

const getData = () => {
  const variables: {
    id: string;
  } = {
    id: route.params.id as string,
  };
  loaderPreregistrations(() => PreregistrationApi.getProcess(variables)).then(
    (res) => {
      criteria.value = res.criteria;
      process.value = res;
      schools.value = res.schools.sortBy('name');
      grades.value = res.grades.sortBy('name');
      periods.value = res.periods.sortBy('name');
    }
  );
};

const priorityFields = computed(() =>
  process.value?.fields
    .filter((f: ProcessField) => f.weight !== 0)
    .sortBy('weigth')
);

getData();
</script>
