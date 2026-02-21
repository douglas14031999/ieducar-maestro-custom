<template>
  <protocol-find v-model:student="student" type="BIRTH_CERTIFICATE">
    <template #default="slot">
      <x-field
        v-model="student.student_birth_certificate"
        container-class="col-12 col-md-8"
        name="student_birth_certificate"
        rules="required"
        type="BIRTH_CERTIFICATE"
        label="Certidão de nascimento do(a) aluno(a)"
        :errors="!!slot.errors['student_birth_certificate']"
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
  student_birth_certificate: null,
  preregistrations: [],
});
</script>
