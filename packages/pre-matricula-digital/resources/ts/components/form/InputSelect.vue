<template>
  <select v-model="model" v-bind="$attrs">
    <option v-for="option in options" :key="option.id" :value="option.id">
      {{ option.label }}
    </option>
  </select>
</template>

<script lang="ts">
import { PropType, defineComponent } from 'vue';
import { useVModel } from '@vueuse/core';
export default defineComponent({
  props: {
    modelValue: {
      type: String as PropType<string>,
      default: null,
    },
    options: {
      type: Array as PropType<{ id: string; label: string }[]>,
      default: () => [],
    },
  },
  emits: ['input'],
  setup(props) {
    const model = useVModel(props, 'modelValue');

    return {
      model,
    };
  },
});
</script>
