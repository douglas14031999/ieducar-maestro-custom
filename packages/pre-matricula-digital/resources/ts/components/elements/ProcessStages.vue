<template>
  <div class="row">
    <template v-for="stage in stages" :key="stage.id">
      <div class="col-12 col-md-6 mt-3 mt-sm-2">
        <process-stage-card :stage="stage" />
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { Processes, Stages } from '@/types';
import ProcessStageCard from '@/components/elements/ProcessStageCard.vue';
import { computed } from 'vue';

const props = withDefaults(
  defineProps<{
    process: Processes;
  }>(),
  {
    process: () => ({} as Processes),
  }
);

const stages = computed(() => {
  const { stages } = props.process;

  if (!stages) return [];

  const processesOpened: Stages[] = [];
  const processesNotOpened: Stages[] = [];
  stages.forEach((stage) => {
    if (stage.status === 'OPEN') {
      processesOpened.push(stage);
    } else {
      processesNotOpened.push(stage);
    }
  });
  processesOpened.sortBy('startAt').reverse();
  processesNotOpened.sortBy('startAt').reverse();
  return processesOpened.concat(processesNotOpened);
});
</script>
