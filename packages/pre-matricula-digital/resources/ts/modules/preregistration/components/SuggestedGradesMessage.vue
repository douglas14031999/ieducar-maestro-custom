<template>
  <div v-if="suggestedGrades.length">
    <div class="alert alert-info">
      {{ suggestedGradesMessage }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { PreRegistrationStageProcessGrade } from '@/modules/preregistration/types';

defineProps<{
  suggestedGrades: PreRegistrationStageProcessGrade[];
  suggestedGradesMessage: string;
}>();
</script>
