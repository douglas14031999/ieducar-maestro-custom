<template>
  <table class="table table-striped table-vcenter">
    <thead>
      <tr>
        <th
          v-for="i in cols"
          :key="i + '-skeleton-table-col'"
          :class="{ 'w-1': i === 1 }"
        >
          <x-skeleton
            v-if="i === 1"
            width="16px"
            height="16px"
            square
            class="mt-2"
          />
          <x-skeleton v-else type="text" />
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="i in 10" :key="i + '-skeleton-table-row'">
        <td
          v-for="j in cols"
          :key="j + '-skeleton-table-cell'"
          :class="{ 'w-1': j === 1 }"
        >
          <x-skeleton
            v-if="j === 1"
            width="16px"
            height="16px"
            square
            class="mt-2"
          />
          <x-skeleton v-else type="text" />
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script setup lang="ts">
import XSkeleton from '@/components/loaders/XSkeleton.vue';

withDefaults(
  defineProps<{
    cols?: number;
  }>(),
  {
    cols: 9,
  }
);
</script>
