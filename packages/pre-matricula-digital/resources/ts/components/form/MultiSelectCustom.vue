<template>
  <div>
    <Multiselect
      v-model="model"
      mode="multiple"
      :close-on-select="false"
      :searchable="true"
      :options="compOptions"
      no-results-text="Nenhum resultado encontrado"
      no-options-text="Nenhum resultado encontrado"
      :hide-selected="false"
      :placeholder="placeholder"
      :append-new-option="false"
      @select="selected"
    >
      <template #multiplelabel="{ values }">
        <div class="multiselect-multiple-label multiselect-placeholder">
          {{ getValuesLength(values) }} itens selecionados
        </div>
      </template>
    </Multiselect>
  </div>
</template>

<script lang="ts">
import {
  ComputedRef,
  PropType,
  WritableComputedRef,
  computed,
  defineComponent,
} from 'vue';
import Multiselect from '@vueform/multiselect';

type OptionSelect = {
  value: string | number;
  label: string;
};

export default defineComponent({
  name: 'MultiSelectCustom',
  components: {
    Multiselect,
  },
  props: {
    options: {
      type: Array as PropType<OptionSelect[]>,
      required: true,
    },
    modelValue: {
      type: Array as PropType<Array<number>>,
      required: true,
    },
    placeholder: {
      type: String,
      default: 'Selecione',
    },
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const compOptions: ComputedRef<OptionSelect[]> = computed(
      () => props.options
    );

    const model: WritableComputedRef<number[]> = computed({
      get() {
        return props.modelValue;
      },
      set(value) {
        emit('update:modelValue', value);
      },
    });

    const selected = (option: number): void => {
      if (option === -1) {
        let values: number[] = [];
        if (compOptions.value.length !== model.value.length + 1) {
          values = compOptions.value
            .filter((f) => f.value !== -1)
            .map((el) => {
              return el.value;
            });
        } else {
          values = [];
        }
        model.value = values;
      }
    };

    const getValuesLength = (values: unknown) =>
      (values as unknown as unknown[]).length;

    return { selected, compOptions, model, getValuesLength };
  },
});
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<style>
.multiselect {
  height: 45px !important;
  border-radius: 8px !important;
  border: 1px solid #acc5e4 !important;
}
.multiselect.is-open {
  border-radius: 8px 8px 0 0 !important;
}
.multiselect.is-open.is-active {
  box-shadow: 0 2px 12px 0.2rem rgba(0, 114, 255, 0.18) !important;
}
[error='true'] .multiselect {
  border-color: #ec6f8c !important;
}
[error='true'] .multiselect.is-open.is-active {
  border-color: #ec6f8c !important;
  box-shadow: 0 0 0 0.2rem rgba(236, 111, 140, 0.25) !important;
}
.multiselect-placeholder {
  font-family: inherit;
  color: #787878 !important;
}
.multiselect-option.is-selected {
  background: #0072ff !important;
}
.multiselect-search {
  border-radius: 8px !important;
}
</style>
