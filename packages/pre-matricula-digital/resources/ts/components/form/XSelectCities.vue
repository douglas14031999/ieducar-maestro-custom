<template>
  <div>
    <x-select
      v-bind="$attrs"
      v-model:data="model"
      :options="fetchOptions"
      :loading="loading"
      :unique="unique"
      :disabled="disabled"
      :error="error"
      :placeholder="placeholder"
      :label="label"
      :track-by="trackBy"
      searchable
      @on-search="onSearch"
    >
      <template #open-indicator-helper>
        <x-icon
          v-tooltip.bottom="{
            content:
              'Aguarde a busca ser concluída<br> para fazer a seleção da opção<br> desejada.',
            html: true,
          }"
          name="mdi-help-circle-outline"
          class="mt-1"
          :class="{
            'mr-2': loading,
          }"
          color="primary"
        />
      </template>
    </x-select>
  </div>
</template>

<script setup lang="ts">
import { ModelValue, Option } from '@/types';
import { onMounted, ref, watch } from 'vue';
import XIcon from '@/components/elements/icons/XIcon.vue';
import XSelect from '@/components/form/XSelect.vue';
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
    data?: ModelValue;
    disabled?: boolean;
    error?: boolean;
    placeholder?: string;
    label?: string;
    trackBy?: string;
    unique?: string;
  }>(),
  {
    data: null,
    placeholder: 'Selecione uma Cidade',
    trackBy: 'key',
    allowClear: false,
    label: 'label',
    unique: 'off',
  }
);

const store = useGeneralStore();
const loading = ref(false);
const awaitingSearch = ref(false);
const noResults = ref(false);
const searching = ref({
  loading: false,
  search: '',
});
const fetchOptions = ref<Option[]>([]);
const model = useVModel(props, 'data');
const onSearch = async (payload: { loading: boolean; search: string }) => {
  searching.value = {
    loading: payload.loading,
    search: payload.search,
  };
};
const search = async (query: string) => {
  let name: string = query;
  if (model.value && Number(model.value)) {
    name = model.value as string;
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
    noResults.value = true;
  }
  store.addCities(addCities);
  loading.value = false;
  fetchOptions.value = addCities;
  if (model.value && Number(model.value)) {
    if (
      fetchOptions.value.some(
        (option) => option[props.trackBy as keyof typeof option] !== name
      )
    ) {
      model.value = null;
    }
  }
};
watch(
  searching,
  (val) => {
    if (awaitingSearch.value) {
      return;
    }
    setTimeout(() => {
      if (val.search.length > 0) {
        search(searching.value.search);
      }
      awaitingSearch.value = false;
    }, 700);
    awaitingSearch.value = true;
  },
  {
    deep: true,
  }
);
onMounted(() => {
  search('');
});
</script>
