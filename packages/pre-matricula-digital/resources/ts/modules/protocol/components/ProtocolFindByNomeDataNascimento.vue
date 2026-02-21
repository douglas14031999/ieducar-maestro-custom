<template>
  <protocol-find v-model:student="student" type="NAME_DATE_OF_BIRTH">
    <template #default="slot">
      <x-field
        v-model="student.student_name"
        name="student_name"
        rules="required"
        label="Nome do(a) aluno(a)"
        container-class="col-12 col-md-6"
        type="TEXT"
        :errors="!!slot.errors['student_name']"
      />
      <x-field
        v-model="student.student_date_of_birth"
        name="student_date_of_birth"
        rules="required|date"
        label="Data de nascimento do(a) aluno(a)"
        type="DATE"
        container-class="col-12 col-md-6"
        :errors="!!slot.errors['student_date_of_birth']"
      />
    </template>
  </protocol-find>
</template>

<script setup lang="ts">
import ProtocolFind from '@/modules/protocol/components/ProtocolFind.vue';
import { Student } from '@/types';
import XField from '@/components/x-form/XField.vue';
import { ref } from 'vue';

const student = ref<Student>({
  student_name: null,
  student_date_of_birth: null,
  preregistrations: [],
});
</script>
