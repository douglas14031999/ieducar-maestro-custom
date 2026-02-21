<template>
  <div>
    <div
      v-if="open"
      data-test="vacancy-by-grades-detail"
      class="cv-container mt-3"
    >
      <template v-for="grade in filteredGrades">
        <grade-vacancy
          v-if="grade.total"
          :key="grade.id"
          :data="grade"
          :grade="grade.name"
          class="d-inline-block"
        />
      </template>
    </div>
    <x-btn
      :label="open ? 'Ocultar Detalhes' : 'Ver mais detalhes'"
      data-test="vacancy-card-see-more"
      color="primary"
      class="w-100 mt-3"
      :loading="loading"
      size="lg"
      loading-normal
      no-caps
      no-wrap
      @click="details(open)"
    />
  </div>
</template>

<script setup lang="ts">
import {
  VacancyByGradesProcessGrade,
  VacancyFilter,
  VacancyFilterByGrade,
  VacancyListByGrades,
} from '@/modules/vacancy/types';
import { computed, ref } from 'vue';
import GradeVacancy from '@/components/charts/GradeVacancy.vue';
import { Vacancy as VacancyApi } from '@/modules/vacancy/api';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import { useLoader } from '@/composables';

const props = withDefaults(
  defineProps<{
    filter: VacancyFilter;
    process: string;
  }>(),
  {
    filter: () => ({} as VacancyFilter),
  }
);

const { loading, loader } = useLoader<VacancyListByGrades>();

const open = ref(false);
const grades = ref<VacancyByGradesProcessGrade[]>([]);

const filteredGrades = computed(() => {
  if (props.filter.grade) {
    return grades.value.filter(
      (g) => Number(g.id) === Number(props.filter.grade)
    );
  }

  return grades.value;
});

const details = (payload: boolean) => {
  if (!payload) {
    ctx.load();
    return;
  }
  grades.value = [];
  open.value = !payload;
};

const load = () => {
  const variables: VacancyFilterByGrade = {
    process: props.process,
  } as VacancyFilterByGrade;

  if (props.filter.school) {
    variables.schools = [props.filter.school];
  }

  if (props.filter.grade) {
    variables.grades = [props.filter.grade];
  }

  if (props.filter.period) {
    variables.periods = [props.filter.period];
  }

  loader(() => VacancyApi.listByGrades(variables)).then(
    ({ grades: gradesResponse, statistics }) => {
      statistics.forEach((vacancy) => {
        const grade = gradesResponse.find(
          (g) => Number(g.id) === Number(vacancy.grade)
        );

        if (grade) {
          grade.available += vacancy.available;
          grade.total += vacancy.total;
          grade.waiting += vacancy.waiting;
          grade.accepted += vacancy.accepted;
          grade.rejected += vacancy.rejected;

          if (vacancy.available < 0) {
            grade.exceded = true;
          }
        }
      });

      grades.value = gradesResponse.sortBy('name');
      open.value = true;
    }
  );
};

const ctx = { load };
</script>

<style>
.cv-container {
  white-space: nowrap;
  overflow-x: auto;
  text-align: center;
}
</style>
