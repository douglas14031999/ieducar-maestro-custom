<template>
  <div>
    <h2 class="font-muli-20-primary mb-4">
      Identifique o(a) aluno(a) para continuar com a inscrição:
    </h2>
    <p>
      Preencha os dados abaixo para identificar o(a)
      <strong>aluno(a)</strong> que será matrículado(a).
    </p>
    <div class="row mt-5">
      <div class="col-12 col-md-6">
        <x-autocomplete-field
          v-model="studentNameModel"
          label="Nome do(a) aluno(a)"
          name="student_name"
          :rules="'required'"
          :errors="!!errors['student_name']"
          :fetch-suggestions="searchLegacyStudentsByName"
          :searchable="isSearchable"
          @select="onSelectStudent"
        />
      </div>
      <x-field
        :key="dateFieldKey"
        v-model="modelStudent.student_date_of_birth"
        container-class="col-12 col-md-6"
        label="Data de nascimento do(a) aluno(a)"
        name="student_date_of_birth"
        :rules="`required|date|minimum_age:${minimumAge}`"
        type="DATE"
        :errors="!!errors['student_date_of_birth']"
      />
      <x-field
        v-if="requiredFields.student_cpf.show"
        v-model="modelStudent.student_cpf"
        container-class="col-12 col-md-6"
        label="CPF do(a) aluno(a)"
        name="student_cpf"
        :rules="requiredFields.student_cpf.required ? 'required|cpf' : 'cpf'"
        type="CPF"
        :errors="!!errors['student_cpf']"
      />
      <x-field
        v-if="requiredFields.student_rg.show"
        v-model="modelStudent.student_rg"
        container-class="col-12 col-md-6"
        label="RG do(a) aluno(a)"
        name="student_rg"
        :rules="
          requiredFields.student_rg.required
            ? 'required|rg|max:20'
            : 'rg|max:20'
        "
        type="RG"
        :errors="!!errors['student_rg']"
      />
      <x-field
        v-if="requiredFields.student_birth_certificate.show"
        v-model="modelStudent.student_birth_certificate"
        container-class="col-12 col-md-8"
        label="Certidão de Nascimento do(a) aluno(a)"
        name="student_birth_certificate"
        :rules="
          requiredFields.student_birth_certificate.required
            ? 'required|max:32'
            : 'max:32'
        "
        type="BIRTH_CERTIFICATE"
        :errors="!!errors['student_birth_certificate']"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  Fields,
  PreRegistrationStage,
  PreRegistrationStageProcess,
  PreRegistrationStudentField,
} from '../types';
import { computed, ref } from 'vue';
import { formatCpf, formatPhone } from '@/utils/formatters';
import XAutocompleteField from '@/components/x-form/XAutocompleteField.vue';
import XField from '@/components/x-form/XField.vue';
import { searchLegacyStudentsByName } from '../api/services/graphql/legacy-student';
import { useGeneralStore } from '@/store/general';
import { useVModel } from '@vueuse/core';

const props = defineProps<{
  student: PreRegistrationStudentField;
  fields: Fields;
  process?: PreRegistrationStageProcess;
  stage?: PreRegistrationStage;
  errors: {
    [key: string]: boolean;
  };
}>();

const store = useGeneralStore();
const isSearchable = computed(() => {
  const auth = store.isAuthenticated;
  const allowSearch = props.stage?.allowSearch ?? false;
  return auth && allowSearch;
});
const dateFieldKey = ref(0);

const requiredFields = {
  student_cpf: {
    required: false,
    show: false,
  },
  student_rg: {
    required: false,
    show: false,
  },
  student_birth_certificate: {
    required: false,
    show: false,
  },
};

props.fields.student
  .filter((field) =>
    ['student_cpf', 'student_rg', 'student_birth_certificate'].includes(
      field.id
    )
  )
  .forEach((field) =>
    Object.assign(requiredFields, {
      [field.id]: { required: field.required, show: true },
    })
  );

const modelStudent = useVModel(props, 'student');
const minimumAge = computed(() => props.process?.minimumAge || 0);

const studentNameModel = computed({
  get: () => modelStudent.value.student_name || '',
  set: (v: string) => {
    modelStudent.value.student_name = v;
  },
});

function onSelectStudent(student: Record<string, string>) {
  dateFieldKey.value++;
  if (student.dateOfBirth)
    modelStudent.value.student_date_of_birth = student.dateOfBirth;
  modelStudent.value.student_cpf = student.cpf ? formatCpf(student.cpf) : '';
  modelStudent.value.responsible_name = student.responsibleName || '';
  modelStudent.value.responsible_cpf = student.responsibleCpf
    ? formatCpf(student.responsibleCpf)
    : '';
  if (student.responsibleDateOfBirth)
    modelStudent.value.responsible_date_of_birth =
      student.responsibleDateOfBirth;
  modelStudent.value.responsible_phone = student.responsiblePhone
    ? formatPhone(student.responsiblePhone)
    : '';
}
</script>
