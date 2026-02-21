<template>
  <div v-if="noGradesSuggested">
    <div class="alert alert-danger">
      Não há séries compatíveis com a faixa etária do(a) aluno(a), verifique se
      o processo acessado é o correto, caso sim contate seu Município para dar
      continuidade na inscrição.
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  noGradesSuggested: boolean;
}>();
</script>
