<template>
  <select v-model="model">
    <slot name="option" />
    <option v-for="(option, index) in options" :key="index" :value="option.key">
      {{ option.label }}
    </option>
  </select>
</template>

<script lang="ts" setup>
import { ModelValue, Option } from '@/types';
import { useVModel } from '@vueuse/core';

const props = defineProps<{
  modelValue: ModelValue;
  options: Option[];
}>();

const model = useVModel(props, 'modelValue');
</script>
