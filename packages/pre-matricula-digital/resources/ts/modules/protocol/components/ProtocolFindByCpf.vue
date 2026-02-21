<template>
  <protocol-find v-model:student="student" type="CPF">
    <template #default="slot">
      <x-field
        v-model="student.student_cpf"
        container-class="col-12 col-md-6"
        name="student_cpf"
        rules="required|cpf"
        label="CPF do(a) aluno(a)"
        type="CPF"
        :errors="!!slot.errors['student_cpf']"
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
  student_cpf: null,
  preregistrations: [],
});
</script>
