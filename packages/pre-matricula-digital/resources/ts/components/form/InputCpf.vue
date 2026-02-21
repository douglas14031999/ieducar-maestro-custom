<template>
  <input v-mask="mask" v-bind="$attrs" type="tel" :autocomplete="unique" />
</template>

<script lang="ts">
import { PropType, defineComponent, ref } from 'vue';
import { mask } from 'vue-the-mask';
export default defineComponent({
  directives: {
    mask,
  },
  props: {
    unique: {
      type: String as PropType<string>,
      default: null,
    },
  },
  setup() {
    const mask = ref('###.###.###-##');

    return {
      mask,
    };
  },
});
</script>
