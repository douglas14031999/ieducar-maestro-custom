<template>
  <div class="position-relative">
    <multiselect-component
      ref="multiselect"
      v-bind="$attrs"
      v-model="modelSelected"
      :options="async (query: string) => fetchOptions(query)"
      :placeholder="placeholder"
      :disabled="disabled"
      :searchable="searchable"
      :track-by="trackBy"
      :value-prop="valueProp"
      :can-clear="allowClear || searchable"
      :can-deselect="false"
      :filter-results="false"
      :min-chars="1"
      :resolve-on-load="true"
      :delay="0"
      no-options-text="Nenhuma opção disponível"
      no-results-text="Nenhum resultado encontrado"
      :autocomplete="unique"
      :input-type="searchable ? 'search' : 'text'"
      class="style-chooser"
      :class="{ 'have-not-clear': !allowClear }"
      :classes="{
        options: options && options.length === 0 ? 'p-0 border-0' : '',
      }"
      @open="handleOpen"
      @close="handleClose"
      @paste="handleClose"
    >
      <template #spinner>
        <x-spinner normal class="text-primary mr-3" size="sm" />
      </template>
    </multiselect-component>
  </div>
</template>

<script setup lang="ts">
import '@vueform/multiselect/themes/default.css';
import { ModelValue, Option } from '@/types';
import MultiselectComponent from '@vueform/multiselect';
import XSpinner from '@/components/loaders/XSpinner.vue';
import { ref } from 'vue';
import { useVModel } from '@vueuse/core';

const emit = defineEmits<{
  (action: 'open:options'): void;
  (action: 'close:options'): void;
}>();

const props = withDefaults(
  defineProps<{
    selected: ModelValue;
    options: Option[];
    placeholder?: string;
    disabled?: boolean;
    allowClear?: boolean;
    searchable?: boolean;
    trackBy?: string;
    valueProp?: string;
    unique?: string;
  }>(),
  {
    selected: null,
    options: () => [],
    placeholder: 'Selecione uma Opção',
    disabled: false,
    allowClear: false,
    searchable: false,
    trackBy: 'label',
    valueProp: 'key',
    unique: Math.random().toString(36).substr(2, 9),
  }
);

const multiselect = ref();

const modelSelected = useVModel(props, 'selected');

const fetchOptions = (query: string) => {
  if (!props.searchable || !query || query === '') {
    return props.options;
  }
  const options = props.options.filter((option) =>
    String(option[props.trackBy as keyof typeof option])
      .toLowerCase()
      .includes(query.toLowerCase())
  );
  if (options.length === 0) {
    return [
      {
        [props.trackBy]: 'Nenhuma opção disponível',
        [props.valueProp]: null,
        disabled: true,
      },
    ];
  }
  return options;
};

const handleOpen = () => {
  if (multiselect.value) {
    multiselect.value.refreshOptions();
  }
  emit('open:options');
};

const handleClose = () => {
  emit('close:options');
  if (
    modelSelected.value !== null &&
    !props.options.some(
      (option) =>
        option[props.valueProp as keyof typeof option] === modelSelected.value
    )
  ) {
    modelSelected.value = null;
    if (multiselect.value) {
      multiselect.value.refreshOptions();
    }
  }
};
</script>

<style lang="scss">
.form-control.is-select {
  background-size: auto !important;
}
.clear-selection {
  cursor: pointer;
  float: right;
  font-weight: 700;
  height: 26px;
  margin-right: 40px;
  padding-right: 0;
  background-color: transparent;
  border: none;
  font-size: 1em;
  margin-top: calc(0.5em + -2.8rem);
  color: #787878;
  opacity: 0.8;
}
</style>
