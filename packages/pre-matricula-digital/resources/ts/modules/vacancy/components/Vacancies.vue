<template>
  <main>
    <h1>Painel de Vagas</h1>
    <stats-cards :stats="stats" />
    <x-card class="bg-primary mb-4">
      <x-card-section class="pl-2 pr-2">
        <form @submit.prevent="load()">
          <div class="row m-0 mt-1 filter-gutters">
            <x-field
              v-model="filter.year"
              container-class="col-12 col-md-2 mb-md-0"
              name="year"
              placeholder="Ano"
              type="SELECT"
              :options="years"
            />
            <x-field
              v-model="filter.school"
              container-class="col-12 col-md-4 mb-md-0"
              name="school"
              placeholder="Filtrar por escola"
              type="SELECT"
              :options="schools"
              searchable
              @change="filter.canAcceptInBatch = false"
            />
            <x-field
              v-model="filter.grade"
              container-class="col-12 col-md mb-md-0"
              name="grade"
              placeholder="Filtrar por série"
              type="SELECT"
              :options="grades"
              searchable
              @change="filter.canAcceptInBatch = false"
            />
            <x-field
              v-model="filter.period"
              container-class="col-12 col-md mb-md-0"
              name="period"
              placeholder="Filtrar por turno"
              type="SELECT"
              :options="
                periods.map((period) => ({
                  key: period.id,
                  label: period.name,
                }))
              "
              @change="filter.canAcceptInBatch = false"
            />
          </div>
          <div class="d-flex m-0 mt-3 filter-gutters justify-content-end">
            <div>
              <x-btn
                data-test="vacancy-btn-clear"
                label="Limpar"
                color="white"
                flat
                class="mr-3"
                no-caps
                no-wrap
                @click="cleanFilter"
              />
              <x-btn
                data-test="vacancy-btn-search"
                label="Buscar"
                color="white"
                outline
                class="mr-2"
                no-caps
                no-wrap
                :loading="loading"
                loading-normal
                @click="load()"
              />
            </div>
          </div>
        </form>
      </x-card-section>
    </x-card>
    <div class="row legend-wrapper ml-1">
      <div class="bg-green mr-1 mb-1"></div>
      <span>DEFERIDAS</span>
      <div class="bg-danger mr-1 mb-1"></div>
      <span>INDEFERIDAS</span>
      <div class="bg-yellow mr-1 mb-1"></div>
      <span>EM ESPERA</span>
      <div class="bg-light mr-1 mb-1"></div>
      <span>INDISPONÍVEL</span>
      <div class="open-legend mr-1 mb-1"></div>
      <span>ABERTAS</span>
    </div>
    <skeleton-card-vacancies v-if="loading" />
    <div class="row">
      <div
        v-for="process in processes"
        :key="process.id"
        data-test="vacancy-card"
        class="col-12 col-md-6 mt-4"
      >
        <x-card bordered>
          <x-card-section class="p-3">
            <div class="d-flex justify-content-between">
              <h3
                v-tooltip.bottom-start="process.name"
                data-test="vacancy-card-title"
                class="font-hind-18-primary text-primary m-0 text-truncate"
              >
                {{ process.name }}
              </h3>
              <div
                v-if="process.excededVacancies"
                v-tooltip.bottom-start="
                  'Indica que há turmas que excederam suas vagas dentro do processo'
                "
                class="badge badge-red d-flex align-items-center pl-2 pr-2 pt-0 pb-0"
              >
                Vagas excedidas
              </div>
            </div>
          </x-card-section>
          <x-card-section>
            <vue-apex-charts
              :options="
                charts.find((chart) => chart.id === process.id)?.options
              "
              :series="charts.find((chart) => chart.id === process.id)?.series"
              :enabled="false"
              height="300px"
              type="donut"
            />
            <div class="row p-3">
              <div class="col-12 px-3">
                <div class="d-flex">
                  <div>
                    <div>Inscrições deferidas</div>
                    <div class="text-muted small">
                      em relação ao total de <strong>vagas ofertadas</strong>
                    </div>
                  </div>
                  <div class="ml-auto text-right">
                    <div data-test="vacancy-card-relation">
                      {{ process.accepted }}/{{ process.total }}
                    </div>
                    <div
                      data-test="vacancy-card-relation-percentage"
                      class="text-muted small"
                    >
                      {{
                        Math.trunc((process.accepted / process.total) * 100)
                      }}%
                    </div>
                  </div>
                </div>
                <div
                  class="d-flex mt-2"
                  style="margin-left: -0.25rem; margin-right: -0.25rem"
                >
                  <div
                    v-if="process.percentual.exceded > 0"
                    v-tooltip.bottom-end="
                      `Vagas excedidas: ${process.exceded} (${process.percentual.exceded}%)`
                    "
                    :style="`width: ${process.percentual.exceded}%`"
                    class="cp"
                  >
                    <div
                      class="bg-red"
                      style="max-width: 100%; height: 14px; border-radius: 4px"
                    ></div>
                  </div>
                  <div
                    v-if="process.percentual.accepted > 0"
                    v-tooltip.bottom-end="
                      `Vagas deferidas: ${process.accepted} (${process.percentual.accepted}%)`
                    "
                    :style="`width: ${process.percentual.accepted}%`"
                    class="cp"
                  >
                    <div
                      class="bg-green"
                      style="max-width: 100%; height: 14px; border-radius: 4px"
                    ></div>
                  </div>
                  <div
                    v-if="process.percentual.unavailable > 0"
                    v-tooltip.bottom-end="
                      `
                      Vagas indisponíveis: ${process.unavailable} (${process.percentual.unavailable}%)
                    `
                    "
                    :style="`width: ${process.percentual.unavailable}%`"
                    class="cp"
                  >
                    <div
                      class="bg-light"
                      style="max-width: 100%; height: 14px; border-radius: 4px"
                    ></div>
                  </div>
                  <div
                    v-if="process.percentual.available > 0"
                    v-tooltip.bottom-end="
                      `Vagas disponíveis: ${process.available} (${process.percentual.available}%)`
                    "
                    :style="`width: ${process.percentual.available}%`"
                    class="cp"
                  >
                    <div
                      style="
                        max-width: 100%;
                        border: 1px solid #d8e9ff;
                        height: 14px;
                        border-radius: 4px;
                      "
                    ></div>
                  </div>
                </div>
                <div
                  class="d-flex mt-2"
                  style="margin-left: -0.25rem; margin-right: -0.25rem"
                >
                  <div
                    v-if="process.percentual.totalExceded > 0"
                    v-tooltip.bottom-end="
                      `
                      Total de vagas ultrapassadas: ${process.totalExceded} (${process.percentual.totalExceded}%)
                    `
                    "
                    :style="`width: ${process.percentual.totalExceded}%`"
                    class="cp"
                  >
                    <div
                      class="bg-danger"
                      style="max-width: 100%; height: 4px; border-radius: 4px"
                    ></div>
                  </div>
                  <div
                    v-if="process.percentual.total > 0"
                    v-tooltip.bottom-end="
                      `Total de vagas disponíveis: ${process.total} (${process.percentual.total}%)`
                    "
                    :style="`width: ${process.percentual.total}%`"
                    class="cp"
                  >
                    <div
                      class="bg-info"
                      style="max-width: 100%; height: 4px; border-radius: 4px"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row p-3">
              <div class="col-12 px-3">
                <div class="d-flex">
                  <div>
                    <div>Inscrições em espera</div>
                    <div class="text-muted small">
                      em relação a quantidade de
                      <strong>vagas disponíveis</strong>
                    </div>
                  </div>
                  <div
                    v-if="process.waiting || process.available"
                    class="ml-auto text-right"
                  >
                    <div>{{ process.waiting }}/{{ process.available }}</div>
                    <div v-if="process.available" class="text-muted small">
                      {{ process.percentual.waiting }}%
                    </div>
                    <div v-else class="text-muted small">
                      {{ process.waiting * 100 }}%
                    </div>
                  </div>
                </div>
                <template v-if="process.waiting || process.available">
                  <div
                    class="d-flex mt-2"
                    style="margin-left: -0.25rem; margin-right: -0.25rem"
                  >
                    <div
                      v-if="process.percentual.waiting > 0"
                      v-tooltip.bottom-end="
                        `Vagas em espera: ${process.waiting}`
                      "
                      :style="`width: ${process.percentual.waiting}%`"
                      class="cp"
                    >
                      <div
                        class="bg-yellow"
                        style="
                          max-width: 100%;
                          height: 14px;
                          border-radius: 4px;
                        "
                      ></div>
                    </div>
                    <div
                      v-if="process.percentual.waitingRealAvailable > 0"
                      v-tooltip.bottom-end="
                        `
                      Vagas disponíveis: ${process.waitingAvailable}
                    `
                      "
                      :style="`width: ${process.percentual.waitingRealAvailable}%`"
                      class="cp"
                    >
                      <div
                        style="
                          max-width: 100%;
                          border: 1px solid #d8e9ff;
                          height: 14px;
                          border-radius: 4px;
                        "
                      ></div>
                    </div>
                  </div>
                  <div
                    class="d-flex mt-2"
                    style="margin-left: -0.25rem; margin-right: -0.25rem"
                  >
                    <div
                      v-if="process.percentual.waitingExceded > 0"
                      v-tooltip.bottom-end="
                        `
                      Total de vagas ultrapassadas: ${process.waitingExceded} (${process.percentual.waitingExceded}%)
                    `
                      "
                      :style="`width: ${process.percentual.waitingExceded}%`"
                      class="cp"
                    >
                      <div
                        class="bg-danger"
                        style="max-width: 100%; height: 4px; border-radius: 4px"
                      ></div>
                    </div>
                    <div
                      v-if="process.percentual.waitingAvailable > 0"
                      v-tooltip.bottom-end="
                        `
                      Total de vagas disponíveis: ${process.available} (${process.percentual.waitingAvailable}%)
                    `
                      "
                      :style="`width: ${process.percentual.waitingAvailable}%`"
                      class="cp"
                    >
                      <div
                        class="bg-info"
                        style="max-width: 100%; height: 4px; border-radius: 4px"
                      ></div>
                    </div>
                  </div>
                </template>
                <template v-else>
                  <div class="small mt-3 text-info">
                    Não há vagas disponíveis nem inscrições em lista de espera.
                  </div>
                </template>
              </div>
            </div>
            <div class="row p-3">
              <div class="col-12 px-3">
                <div class="d-flex">
                  <div>
                    <div>Alunos(as) inscritos(as)</div>
                    <div class="text-muted small">
                      contabilizados somente os alunos com
                      <strong>CPF cadastrado</strong>
                    </div>
                  </div>
                  <div class="ml-auto text-right">
                    <div>{{ unique[process.id] || process.waiting }}</div>
                  </div>
                </div>
              </div>
            </div>
          </x-card-section>
          <x-card-section>
            <vacancies-by-grades :filter="filter" :process="process.id" />
          </x-card-section>
        </x-card>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
import {
  GraphCustomParam,
  GraphFormatter,
  VacancyFilter,
  VacancyLoadData,
  VacancyLoadProcessAnalyse,
  VacancyLoadProcesses,
  VacancyLoadVariable,
  VacancyStats,
  VacancyTotalProcess,
} from '@/modules/vacancy/types';
import { computed, onMounted, ref } from 'vue';
import { Option } from '@/types';
import SkeletonCardVacancies from '@/components/loaders/components/CardVacancies.vue';
import StatsCards from '@/components/elements/StatsCards.vue';
import VacanciesByGrades from '@/components/charts/VacanciesByGrades.vue';
import { Vacancy as VacancyApi } from '@/modules/vacancy/api';
// https://apexcharts.com/docs/vue-charts/
import VueApexCharts from 'vue3-apexcharts';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XField from '@/components/x-form/XField.vue';
import { useGeneralStore } from '@/store/general';
import { useLoader } from '@/composables';

const { loader: loaderData, loading: loadingData } =
  useLoader<VacancyTotalProcess[]>();

const { loader: loaderProcesses, loading: loadingProcesses } =
  useLoader<VacancyLoadProcesses>();

const store = useGeneralStore();

const processes = ref<VacancyLoadProcessAnalyse[]>([]);
const schools = ref<VacancyLoadData[]>([]);
const grades = ref<VacancyLoadData[]>([]);
const periods = ref<VacancyLoadData[]>([]);
const unique = ref<{ [process: string]: number }>({});
const filter = ref<VacancyFilter>({
  school: null,
  period: null,
  grade: null,
  year: null,
  canAcceptInBatch: false,
});
const newFilter = ref<VacancyFilter>({
  school: null,
  period: null,
  grade: null,
  year: null,
  canAcceptInBatch: false,
});
const stats = ref<VacancyStats>();
const years = ref<Option[]>([]);
const charts = ref<
  {
    options: Record<string, unknown>;
    series: number[];
    id: string;
  }[]
>([]);

const loading = computed(() => loadingData.value || loadingProcesses.value);

const getData = () => {
  loaderData(VacancyApi.list).then((res) => {
    const processYearWhereExistsPreRegistration: number[] = [];
    const processYearWhereNotExistsPreRegistration: number[] = [];

    res.forEach((process) => {
      if (process.totalPreRegistrations > 0) {
        processYearWhereExistsPreRegistration.push(process.schoolYear.year);
      } else {
        processYearWhereNotExistsPreRegistration.push(process.schoolYear.year);
      }
    });
    processYearWhereExistsPreRegistration.sort().reverse();

    const yearMostRecent = processYearWhereExistsPreRegistration[0];

    years.value = processYearWhereExistsPreRegistration
      .concat(processYearWhereNotExistsPreRegistration)
      .filter((v, i, a) => a.indexOf(v) === i)
      .map((year) => ({ key: year, label: year }))
      .sortBy('key')
      .reverse() as unknown as Option[];

    if (yearMostRecent) {
      filter.value.year = yearMostRecent;
    }

    load();
  });
};

const cleanFilter = () => {
  filter.value = { ...newFilter.value };
  load();
};

const load = () => {
  charts.value = [];

  const variables: VacancyLoadVariable = {} as VacancyLoadVariable;

  if (filter.value.school) {
    variables.schools = [filter.value.school];
  } else if (store.isSecretary) {
    variables.schools = store.getUserSchools;
  }

  if (store.isSecretary) {
    variables.schoolsAllowed = store.getUserSchools;
  }

  if (filter.value.grade) {
    variables.grades = [filter.value.grade];
  }

  if (filter.value.period) {
    variables.periods = [filter.value.period];
  }

  if (filter.value.year) {
    variables.year = filter.value.year;
  }

  processes.value = [];

  loaderProcesses(() => VacancyApi.load(variables)).then((res) => {
    processes.value = res.processes;
    schools.value = res.schools.sortBy('name');
    grades.value = res.grades.sortBy('name');
    periods.value = res.periods.sortBy('name');
    stats.value = res.stats;
    unique.value = res.unique;

    if (schools.value.length === 1) {
      filter.value.school = schools.value[0].id;
    }

    processes.value.forEach((process) => {
      const options = {
        legend: {
          show: false,
        },
        dataLabels: {
          enabled: false,
        },
        colors: ['#CCFAB6', '#EC6F8C', '#FFF495'],
        states: {
          hover: {
            filter: {
              type: 'none',
            },
          },
          active: {
            filter: {
              type: 'none',
            },
          },
        },
        stroke: {
          show: true,
          curve: 'smooth',
          lineCap: 'butt',
          colors: ['#CCFAB6', '#EC6F8C', '#FFF495'],
          width: 2,
          dashArray: 0,
        },
        series: [0, 0, 0],
        labels: ['DEFERIDAS', 'INDEFERIDAS', 'EM ESPERA'],
        plotOptions: {
          pie: {
            expandOnClick: false,
            donut: {
              size: '75%',
              labels: {
                show: true,
                name: {
                  offsetY: 25,
                },
                value: {
                  fontSize: '42px',
                  fontFamily: 'Hind',
                  fontWeight: 'bold',
                  color: 'rgba(0, 0, 0, 0.8)',
                  offsetY: -25,
                },
                total: {
                  show: true,
                  showAlways: true,
                  label: 'INSCRIÇÕES',
                  fontSize: '13px',
                  fontFamily: 'Hind',
                  fontWeight: 'normal',
                  color: 'rgba(0, 0, 0, 0.5)',
                  formatter: (w: GraphFormatter) =>
                    w.globals.seriesTotals.reduce((s, p) => s + p),
                },
              },
            },
          },
        },
        tooltip: {
          custom: (d: GraphCustomParam) => {
            const label = d.w.globals.labels[d.seriesIndex];
            const count = d.series[d.seriesIndex];

            return `<span style="padding: 4px 8px">${count} ${label}</span>`;
          },
        },
      };

      charts.value.push({
        options,
        series: [process.accepted, process.rejected, process.waiting],
        id: process.id,
      });
    });
  });
};

onMounted(() => getData());
</script>

<style lang="scss">
.chart-wrapper {
  padding: 1em;
  border-radius: 8px;
  box-shadow: 0 2px 20px 0 rgba(0, 91, 203, 0.08);
  border: solid 1px #d8e9ff;
  background-color: #ffffff;
}

.chart-wrapper span {
  font-family: Hind, sans-serif;
  font-size: 16px;
  font-weight: 600;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  color: #003473;
}

.deferred-legend {
  background-color: #ccfab6;
}

.open-legend {
  background-color: #ffffff;
  border: solid 1px #acc5e4;
}

.legend-wrapper {
  align-items: center;
}

.legend-wrapper div {
  width: 10px;
  height: 10px;
  border-radius: 4px;
}

.legend-wrapper span {
  font-family: Hind, sans-serif;
  font-size: 13px;
  font-weight: normal;
  font-stretch: normal;
  font-style: normal;
  line-height: normal;
  letter-spacing: normal;
  color: #ababab;
  margin-right: 1em;
}

.cp {
  padding: 0 0.25rem;
}
</style>
