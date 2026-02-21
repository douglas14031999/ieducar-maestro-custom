<template>
  <div class="input-group">
    <div class="input-group-prepend">
      <button
        type="button"
        class="btn btn-primary"
        tabindex="-1"
        @click="callCalendarHour"
      >
        <x-icon name="mdi-clock" />
      </button>
    </div>
    <flat-pickr
      ref="flatpickrHour"
      v-bind="$attrs"
      v-mask="mask"
      :model-value="$attrs.value"
      :config="config"
      class="form-control"
      :autocomplete="unique"
      @update:model-value="$emit('update:modelValue', $event)"
    />
  </div>
</template>

<script lang="ts">
import 'flatpickr/dist/flatpickr.min.css';
import { PropType, defineComponent, ref } from 'vue';
import { ModelValue } from '@/types';
import { Portuguese } from 'flatpickr/dist/l10n/pt';
import XIcon from '@/components/elements/icons/XIcon.vue';
import flatPickr from 'vue-flatpickr-component';
import { mask } from 'vue-the-mask';
import { useVModel } from '@vueuse/core';
export default defineComponent({
  directives: {
    mask,
  },
  components: {
    flatPickr,
    XIcon,
  },
  props: {
    modelValue: {
      type: String as PropType<ModelValue>,
      default: null,
    },
    unique: {
      type: String as PropType<string>,
      default: 'off',
    },
  },
  emits: ['update:modelValue'],
  setup(props) {
    const flatpickrHour = ref();

    const config = ref({
      allowInput: true,
      locale: Portuguese,
      time_24hr: true,
      enableTime: true,
      defaultHour: '12',
      noCalendar: true,
    });

    const mask = ref('##:##');

    const model = useVModel(props, 'modelValue');

    const callCalendarHour = () => {
      if (flatpickrHour.value) {
        flatpickrHour.value.fp.open();
      }
    };

    return {
      mask,
      flatpickrHour,
      model,
      callCalendarHour,
      config,
    };
  },
});
</script>
