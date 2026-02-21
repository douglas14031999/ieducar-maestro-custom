<template>
  <div class="input-group">
    <div class="input-group-prepend">
      <button
        ref="input"
        type="button"
        class="btn btn-primary no-focus"
        tabindex="-1"
      >
        <x-icon name="mdi-calendar-month" />
      </button>
    </div>
    <input
      v-model="maskedModel"
      v-mask="mask"
      type="text"
      class="form-control"
      :class="{ 'is-invalid': errors }"
      style="border-top-right-radius: 8px; border-bottom-right-radius: 8px"
      :autocomplete="unique"
      @blur="handleBlur"
    />
    <input v-bind="$attrs" type="text" class="d-none" :autocomplete="unique" />
  </div>
</template>

<script lang="ts">
import 'flatpickr/dist/flatpickr.min.css';
import {
  PropType,
  computed,
  defineComponent,
  getCurrentInstance,
  onMounted,
  ref,
} from 'vue';
import { getFormattedDate, getRawDate } from '@/datetime';
import { DateOption } from 'flatpickr/dist/types/options.d';
import { Instance } from 'flatpickr/dist/types/instance.d';
import XIcon from '@/components/elements/icons/XIcon.vue';
import flatpickr from 'flatpickr';
import language from 'flatpickr/dist/l10n/pt';
import { mask } from 'vue-the-mask';
export default defineComponent({
  components: {
    XIcon,
  },
  directives: {
    mask,
  },
  props: {
    errors: {
      type: Boolean as PropType<boolean>,
      default: false,
    },
    unique: {
      type: String as PropType<string>,
      default: null,
    },
  },
  emits: ['update:modelValue', 'update:date', 'change'],
  setup(props, { emit, attrs }) {
    const mask = ref('##/##/####');

    const vm = getCurrentInstance();

    const fp = ref<Instance>();
    const maskedModel = ref('');

    const model = computed<string>({
      get() {
        return attrs.value ? getFormattedDate(attrs.value as string) : '';
      },
      set(val) {
        emit('update:modelValue', getRawDate(val));
      },
    });

    const handleBlur = () => {
      emit('update:modelValue', getRawDate(maskedModel.value, 'DD/MM/YYYY'));
    };

    onMounted(() => {
      maskedModel.value = JSON.parse(JSON.stringify(model.value));
      fp.value = flatpickr(vm?.proxy?.$refs?.input as HTMLElement, {
        onChange: (_, date) => {
          maskedModel.value = getFormattedDate(date);
          emit('update:date', getRawDate(date));
          emit('change', date);
        },
        onOpen: (_, __, instance) => {
          instance.setDate(model.value as DateOption);
        },
        locale: language.pt,
        disableMobile: true,
      });

      (vm?.proxy?.$refs?.input as HTMLElement).setAttribute('type', 'button');
    });

    return {
      mask,
      maskedModel,
      handleBlur,
    };
  },
});
</script>

<style>
.btn.no-focus {
  box-shadow: none !important;
}
</style>
