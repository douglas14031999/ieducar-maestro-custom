<template>
  <div
    style="
      background: transparent;
      width: 75px;
      height: 300px;
      padding: 0 10px;
      overflow: hidden;
    "
  >
    <div style="height: 220px; width: 14px; margin: -0.25rem auto">
      <div v-if="opened > 0" :style="`height: ${opened}%; padding: 0.25rem 0`">
        <div v-tooltip.right="`${data.available}`" class="cv-opened"></div>
      </div>
      <div
        v-if="waiting > 0"
        :style="`height: ${waiting}%; padding: 0.25rem 0`"
      >
        <div v-tooltip.right="`${data.waiting}`" class="cv-waiting"></div>
      </div>
      <div
        v-if="rejected > 0"
        :style="`height: ${rejected}%; padding: 0.25rem 0`"
      >
        <div v-tooltip.right="`${data.rejected}`" class="cv-rejected"></div>
      </div>
      <div
        v-if="accepted > 0"
        :style="`height: ${accepted}%; padding: 0.25rem 0`"
      >
        <div v-tooltip.right="`${data.accepted}`" class="cv-accepted"></div>
      </div>
    </div>
    <div
      v-if="data.exceded"
      v-tooltip="
        'Indica que há turmas desta série que tiveram suas vagas excedidas'
      "
      class="text-center mt-4 bg-danger exceded"
      data-test="data-exceded"
    >
      <div class="small text-muted">{{ grade }}</div>
      <div class="font-weight-bolder">{{ data.total }}</div>
    </div>
    <div v-else class="text-center mt-4">
      <div class="small text-muted">{{ grade }}</div>
      <div class="font-weight-bolder">{{ data.total }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { VacancyByGradesProcessGrade } from '@/modules/vacancy/types';
import { computed } from 'vue';

const round = (value: number) => (value <= 0 ? 0 : Math.ceil(value));

const props = defineProps<{
  data: VacancyByGradesProcessGrade;
  grade: string;
}>();

const preregistrations = computed(
  () =>
    props.data.available +
    props.data.accepted +
    props.data.rejected +
    props.data.waiting
);

const opened = computed(() =>
  round((props.data.available / preregistrations.value) * 100)
);

const accepted = computed(() =>
  round((props.data.accepted / preregistrations.value) * 100)
);

const rejected = computed(() =>
  round((props.data.rejected / preregistrations.value) * 100)
);

const waiting = computed(() =>
  round((props.data.waiting / preregistrations.value) * 100)
);
</script>

<style scoped>
.cv-opened,
.cv-waiting,
.cv-rejected,
.cv-accepted {
  display: table;
  height: 100%;
  width: 14px;
  border-radius: 4px;
}

.cv-opened {
  box-shadow: inset 0 0 0 2px #e7f2ff;
}

.cv-waiting {
  background: #fff495;
}

.cv-rejected {
  background: #ec6f8c;
}

.cv-accepted {
  background: #ccfab6;
}

.exceded {
  color: white !important;
  border-radius: 4px;
}

.exceded .text-muted {
  color: white !important;
}
</style>
