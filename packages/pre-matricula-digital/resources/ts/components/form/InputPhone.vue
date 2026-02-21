<template>
  <input
    v-mask="mask"
    v-bind="$attrs"
    :value="model"
    type="tel"
    :autocomplete="unique"
  />
</template>

<script lang="ts">
import { PropType, defineComponent, ref } from 'vue';
import { ModelValue } from '@/types';
import { mask } from 'vue-the-mask';
import { useVModel } from '@vueuse/core';
export default defineComponent({
  directives: {
    mask,
  },
  props: {
    data: {
      type: String as PropType<ModelValue>,
      default: null,
    },
    unique: {
      type: String as PropType<string>,
      default: null,
    },
  },
  setup(props) {
    const mask = ref(['(##) ####-####', '(##) #####-####']);

    const model = useVModel(props, 'data');

    return {
      mask,
      model,
    };
  },
});
</script>
