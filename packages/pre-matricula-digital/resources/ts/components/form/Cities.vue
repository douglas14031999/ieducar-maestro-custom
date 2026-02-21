<template>
  <div class="position-relative">
    <multiselect-component
      ref="multiselect"
      v-bind="bind"
      v-model="modelSelected"
      class="style-chooser"
      @select="handleSelect"
      @close="handleClose"
      @open="handleOpen"
      @paste="handleClose"
    >
      <template #spinner>
        <x-spinner normal class="text-primary mr-3" size="sm" />
      </template>
      <template #caret>
        <x-icon
          v-tooltip.bottom="{
            content:
              'Aguarde a busca ser concluída<br> para fazer a seleção da opção<br> desejada.',
            html: true,
          }"
          name="mdi-help-circle-outline"
          class="mr-1 mt-1"
          color="primary"
        />
        <x-icon
          class="mt-1"
          :name="caretOpened ? 'mdi-menu-up' : 'mdi-menu-down'"
        />
      </template>
    </multiselect-component>
  </div>
</template>

<script setup lang="ts">
import '@vueform/multiselect/themes/default.css';
import { Nullable, Option } from '@/types';
import { computed, ref, useAttrs } from 'vue';
import MultiselectComponent from '@vueform/multiselect';
import XIcon from '@/components/elements/icons/XIcon.vue';
import XSpinner from '@/components/loaders/XSpinner.vue';
import { graphql } from '@/api';
import { useGeneralStore } from '@/store/general';
import { useVModel } from '@vueuse/core';

interface City {
  key: number;
  label: string;
  state: {
    abbreviation: string;
  };
}

const props = withDefaults(
  defineProps<{
    cities: Option[];
    placeholder?: string;
    disabled?: boolean;
    trackBy?: string;
    valueProp?: string;
    selected: Nullable<number>;
    allowClear?: boolean;
    unique: string;
  }>(),
  {
    placeholder: 'Selecione uma Cidade',
    trackBy: 'label',
    valueProp: 'key',
    allowClear: false,
  }
);

const attrs = useAttrs();

const store = useGeneralStore();

const multiselect = ref();
const loading = ref(false);
const itemSelected = ref({});
const caretOpened = ref(false);
const noOption = ref({
  key: -1,
  label: 'Nenhuma opção disponível',
  disabled: true,
});

const options = computed<Option[]>(() => store.getCities);

const modelSelected = useVModel(props, 'selected');

const bind = computed(() => ({
  ...attrs,
  options: async (query: string) => getCities(query),
  placeholder: props.placeholder,
  disabled: props.disabled,
  searchable: true,
  canDeselect: false,
  trackBy: props.trackBy,
  valueProp: props.valueProp,
  canClear: props.allowClear,
  loading: loading.value,
  closeOnSelect: true,
  filterResults: false,
  delay: 1000,
  minChars: 1,
  resolveOnLoad: true,
  label: 'label',
  noOptionsText: 'Nenhuma opção disponível',
  noResultsText: 'Nenhum resultado encontrado',
  autocomplete: props.unique,
  inputType: 'search',
}));

const getCities = async (query: string) => {
  let name: string = query;
  if (modelSelected.value && Number(modelSelected.value)) {
    name = String(modelSelected.value);
  }
  loading.value = true;
  const response = await graphql<{
    data: {
      data: {
        cities: {
          data: City[];
        };
      };
    };
  }>({
    variables: {
      search: name,
    },
    query: `
      query cities(
        $search: String
      ) {
        cities(
          search: $search
          orderBy: [
            {
              column: "name"
              order: ASC
            }
          ]
        ) {
          data {
            key:id
            label:name
            state {
              abbreviation
            }
          }
        }
      }
    `,
  });
  const { cities } = response.data.data;
  const addCities = cities.data.map((c: City) => ({
    key: c.key,
    label: `${c.label}/${c.state.abbreviation}`,
  }));
  if (addCities.length === 0) {
    addCities.push(noOption.value);
  }
  store.addCities(addCities);
  loading.value = false;
  return addCities;
};

const handleClose = () => {
  if (
    modelSelected.value !== null &&
    !options.value.some((option) => option.key === modelSelected.value)
  ) {
    modelSelected.value = null;

    if (multiselect.value) {
      multiselect.value.refreshOptions();
    }
  }
  caretOpened.value = false;
};

const handleOpen = () => {
  caretOpened.value = true;
};

const handleSelect = (option: Option) => {
  itemSelected.value = option;
  if (multiselect.value) {
    multiselect.value.refreshOptions();
  }
};
</script>
