<template>
  <div class="position-relative">
    <v-select
      v-bind="{ ...$attrs, modelValue: model }"
      :options="fetchOptions"
      :clearable="clearable"
      :searchable="searchable"
      :loading="loading"
      :placeholder="placeholder"
      :autocomplete="unique"
      :disabled="disabled"
      :get-option-key="handleGetOptionKey"
      :get-option-label="handleGetOptionLabel"
      :reduce="handleReduce"
      :clear-search-on-blur="handleClearSearchOnBlur"
      :filter-by="filterBy"
      autoscroll
      class="style-chooser"
      :class="$attrs.class"
      @search="onSearch"
      @option:selected="handleOptionSelected"
    >
      <template #no-options>
        <div class="p-2 text-gray-1">
          {{
            !!noResults
              ? 'Nenhum resultado encontrado'
              : 'Digite para efetuar a busca'
          }}
        </div>
      </template>
      <template #open-indicator="{ attributes }">
        <slot name="open-indicator-helper" />
        <x-icon
          v-if="!loading"
          class="mt-1"
          name="mdi-chevron-down"
          v-bind="attributes"
        />
      </template>
      <template #spinner>
        <x-spinner v-if="loading" normal class="text-primary" size="xs" />
      </template>
    </v-select>
  </div>
</template>

<script setup lang="ts">
import 'vue-select/dist/vue-select.css';
import { ModelValue, Option } from '@/types';
import { computed, nextTick, ref } from 'vue';
import XIcon from '@/components/elements/icons/XIcon.vue';
import XSpinner from '@/components/loaders/XSpinner.vue';
import { useVModel } from '@vueuse/core';
import vSelect from 'vue-select';

const emit = defineEmits<{
  (
    action: 'onSearch',
    payload: {
      loading: boolean;
      search: string;
    }
  ): void;
  (action: 'update:data', payload: ModelValue): void;
  (action: 'change');
}>();

const props = withDefaults(
  defineProps<{
    data?: ModelValue;
    options: Option[];
    clearable?: boolean;
    searchable?: boolean;
    disabled?: boolean;
    placeholder?: string;
    label?: string;
    trackBy?: string;
    unique?: string;
    loading?: boolean;
  }>(),
  {
    data: undefined,
    options: () => [],
    clearable: false,
    searchable: false,
    disabled: false,
    placeholder: 'Selecione uma opção',
    label: 'label',
    trackBy: 'key',
    unique: 'off',
    loading: false,
  }
);

const searching = ref({
  loading: false,
  search: '',
});

const model = useVModel(props, 'data');
const handleGetOptionKey = (option: Option) =>
  option[props.trackBy as keyof typeof option];
const handleGetOptionLabel = (option: Option) =>
  option[props.label as keyof typeof option];
const handleReduce = (option: Option) =>
  option[props.trackBy as keyof typeof option];
const handleClearSearchOnBlur = () => {
  if (props.searchable && searching.value.search.length > 0) {
    model.value = null;
  }
  return true;
};
const handleOptionSelected = (el: Option) => {
  nextTick(() => {
    model.value = el[props.trackBy as keyof typeof el] as string;
    emit('change', el.key);
  });
};
const onSearch = async (search: string, searchLoad: boolean) => {
  searching.value = {
    loading: searchLoad,
    search,
  };
  emit('onSearch', searching.value);
};
const fetchOptions = computed(() => props.options);
const noResults = computed(() => fetchOptions.value.length === 0);
const filterBy = (option: object, label: string, search: string) => {
  return (
    (label?.slugify() || '')
      .toLocaleLowerCase()
      .indexOf(search.slugify().toLocaleLowerCase()) > -1
  );
};
</script>
