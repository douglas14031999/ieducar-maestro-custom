<template>
  <protocol-find v-model:student="student" type="RG">
    <template #default="slot">
      <x-field
        v-model="student.student_rg"
        container-class="col-12 col-md-6"
        name="student_rg"
        rules="required"
        label="RG do(a) aluno(a)"
        type="RG"
        :errors="!!slot.errors['student_rg']"
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
  student_rg: null,
  preregistrations: [],
});
</script>
