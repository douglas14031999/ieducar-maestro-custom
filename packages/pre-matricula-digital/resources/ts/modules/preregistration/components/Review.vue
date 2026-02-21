<template>
  <div class="container-pmd">
    <h2 class="font-muli-20-primary mb-4">
      Confira seus dados antes de finalizar a inscrição:
    </h2>
    <div class="mb-3 d-sm-flex justify-content-between">
      <h3 class="font-hind-18-primary">
        Dados do(a) responsável pelo(a) aluno(a)
      </h3>
      <x-btn
        data-test="btn-edit-responsible"
        color="primary"
        outline
        class="flex-row"
        label="Editar dados"
        no-caps
        no-wrap
        @click="$emit('edit-responsible')"
      />
    </div>
    <div class="row">
      <dl v-for="field in fields.responsible" :key="field.id" class="col-6">
        <dt class="font-hind font-size-10">
          {{ field.field.name }}
        </dt>
        <dd v-if="field.type !== 'CITY'">
          {{ getField(responsible, field) }}
        </dd>
        <dd v-else>
          {{ getCity(responsible, field) }}
        </dd>
      </dl>
      <dl class="col-6">
        <dt class="font-hind font-size-10">Relação com o(a) aluno(a)</dt>
        <dd>
          {{ relationType(responsible.relationType as string) }}
        </dd>
      </dl>
      <dl class="col-sm-12">
        <dt class="font-hind font-size-10">Endereço</dt>
        <dd>
          {{ address }}
        </dd>
      </dl>
      <dl
        v-if="responsible.secondAddress && responsible.secondAddress.postalCode"
        class="col-sm-12"
      >
        <dt class="font-hind font-size-10">Endereço secundário</dt>
        <dd>
          {{ secondAddress }}
        </dd>
      </dl>
    </div>
    <div class="mt-5 mb-3 d-sm-flex justify-content-between">
      <h3 class="font-hind-18-primary">Dados do(a) aluno(a)</h3>
      <x-btn
        data-test="btn-edit-student"
        color="primary"
        outline
        class="flex-row"
        label="Editar dados"
        no-caps
        no-wrap
        @click="$emit('edit-student')"
      />
    </div>
    <div class="row">
      <dl v-for="field in fields.student" :key="field.id" class="col-6">
        <dt class="font-hind font-size-10">
          {{ field.field.name }}
        </dt>
        <dd v-if="field.type !== 'CITY'">
          {{ getField(student, field) }}
        </dd>
        <dd v-else>
          {{ getCity(student, field) }}
        </dd>
      </dl>
    </div>
    <div class="mt-5 mb-3 d-sm-flex justify-content-between">
      <h3 class="font-hind-18-primary">Dados da pré-matrícula</h3>
      <x-btn
        data-test="btn-edit-student-2"
        color="primary"
        outline
        class="flex-row"
        label="Editar dados"
        no-caps
        no-wrap
        @click="$emit('edit-student')"
      />
    </div>
    <div class="row">
      <dl class="col-6">
        <dt class="font-hind font-size-10">Série</dt>
        <dd>
          {{ grade(student.grade as string) }}
        </dd>
      </dl>
      <dl class="col-6">
        <dt class="font-hind font-size-10">Turno</dt>
        <dd>
          {{ period(student.period as string) }}
        </dd>
      </dl>
      <dl v-if="student.school" class="col-sm-12">
        <dt class="font-hind font-size-10">Escola</dt>
        <dd>
          {{ school(student.school) }}
        </dd>
      </dl>
      <dl v-if="student.secondSchool" class="col-sm-12">
        <dt class="font-hind font-size-10">Escola (lista de espera)</dt>
        <dd>
          <div>
            {{ school(student.secondSchool) }} ({{
              period(student.secondPeriod || (student.period as string))
            }})
          </div>
          <div v-for="(waitingList, index) in student.waitingList" :key="index">
            {{ school(waitingList.school) }} ({{ period(waitingList.period) }})
          </div>
        </dd>
      </dl>
    </div>
    <div class="row">
      <x-field
        v-model="accept"
        label="
        Eu autorizo o uso e tratamento dos dados pessoais acima para fins destinados a pré-matrícula na rede municipal
        "
        container-class="col-12 col-md-8 offset-md-2 mt-4"
        rules="required"
        :errors="Boolean(errors['accept'])"
        name="accept"
        type="CHECKBOX"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  Fields,
  ParseFieldFromProcess,
  PreRegistrationResponsibleField,
  PreRegistrationStageProcess,
  PreRegistrationStudentField,
} from '../types';
import { computed, inject, ref } from 'vue';
import { Filters } from '@/filters';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XField from '@/components/x-form/XField.vue';
import { useGeneralStore } from '@/store/general';

defineEmits<{
  (action: 'edit-student'): void;
  (action: 'edit-responsible'): void;
}>();

const props = withDefaults(
  defineProps<{
    errors: { [key: string]: boolean };
    fields: Fields;
    responsible: PreRegistrationResponsibleField;
    student: PreRegistrationStudentField;
    process?: PreRegistrationStageProcess;
  }>(),
  {
    fields: () => ({} as Fields),
    process: () => ({} as PreRegistrationStageProcess),
    responsible: () => ({} as PreRegistrationResponsibleField),
    student: () => ({} as PreRegistrationStudentField),
  }
);

const $filters = inject('$filters') as Filters;

const store = useGeneralStore();

const accept = ref(false);

const cities = computed(() => store.getCities);

const address = computed(() => {
  return $filters.joinAddress(props.responsible.address);
});

const secondAddress = computed(() => {
  return $filters.joinAddress(props.responsible.secondAddress);
});

const relationType = (type: string) => {
  return store.relationTypes.find((r) => r.key === type)?.label;
};
const grade = (id: string) => {
  return props.process.grades.find((g) => Number(g.id) === Number(id))?.name;
};
const period = (id: string) => {
  return props.process.periods.find((p) => Number(p.id) === Number(id))?.name;
};
const school = (id: string) => {
  return props.process.schools.find((s) => Number(s.id) === Number(id))?.name;
};

const getCity = (
  payload: PreRegistrationResponsibleField | PreRegistrationStudentField,
  field: ParseFieldFromProcess
) => {
  return payload[field.key as keyof typeof payload]
    ? cities.value.find(
        (city) => city.key == payload[field.key as keyof typeof payload]
      )?.label
    : '-';
};

const getField = (
  payload: PreRegistrationResponsibleField | PreRegistrationStudentField,
  field: ParseFieldFromProcess
) => {
  return field.filter(payload[field.key as keyof typeof payload]);
};
</script>
