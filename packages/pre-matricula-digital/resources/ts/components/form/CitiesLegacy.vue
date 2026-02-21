<template>
  <div ref="refOptions" class="position-relative">
    <input v-bind="$attrs" type="text" autocomplete="off" class="d-none" />
    <input
      v-model="filter"
      type="search"
      class="form-control is-select"
      :class="$attrs.class"
      :autocomplete="unique"
      @blur="blur"
      @focus="open = true"
    />
    <div
      v-if="loading"
      class="position-absolute"
      style="top: 12px; right: 40px"
    >
      <x-spinner normal color="primary" />
    </div>
    <div v-if="open" class="form-control-list">
      <ul class="list-unstyled">
        <li v-for="option in filtered" :key="(option.key as string)">
          <div class="card-label" @click="handleSelect(option)">
            <span class="form-control-list-item">
              {{ option.label }}
            </span>
          </div>
        </li>
        <li v-if="filtered.length === 0">
          <label class="m-0 d-block">
            <span class="form-control-list-item">
              Nenhum resultado encontrado
            </span>
          </label>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ModelValue, Nullable, Option } from '@/types';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
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
    selected: ModelValue;
    cities?: Option[];
    unique?: string;
    placeholder?: string;
  }>(),
  {
    selected: null,
    cities: () => [],
    unique: 'off',
    placeholder: '',
  }
);

const refOptions = ref();
const filter = ref<Nullable<string>>(null);
const open = ref(false);
const loading = ref(false);

const store = useGeneralStore();

const filtered = computed(() => {
  if (!filter.value) {
    return options.value;
  }
  return options.value
    .filter((option: Option) =>
      option.label
        .toString()
        .slugify()
        .includes((filter.value as string).toString().slugify())
    )
    .slice(0, 15);
});
const options = computed(() => store.cities);
const modelSelected = useVModel(props, 'selected');

const getCities = async (name: string) => {
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
  store.addCities(addCities);
  loading.value = false;
};

const blur = () => {
  setTimeout(() => {
    open.value = false;
    defineLabel(modelSelected.value as string, true);
  }, 250);
};

const defineLabel = (value: string, fromBlur = false) => {
  const item = options.value.find((option: Option) => option.key === value);
  if (item) {
    filter.value = item.label;
  } else if (!value) {
    filter.value = null;
  } else if (fromBlur) {
    modelSelected.value = null;
  }
};

const handleSelect = (option: Option) => {
  modelSelected.value = option.key as string;
};

const onClick = (event: MouseEvent) => {
  event.preventDefault();

  if (refOptions.value && refOptions.value.contains(event.target)) {
    return;
  }
  defineLabel(modelSelected.value as string);
  open.value = false;
};

watch(open, (value) => {
  if (value) {
    filter.value = null;
  }
});

watch(modelSelected, (value) => {
  defineLabel(value as string);
});

watch(filter, (value) => {
  getCities(value as string);
});

onMounted(async () => {
  await getCities((modelSelected.value as string) || '');
  defineLabel((modelSelected.value as string) || '');
  window.addEventListener('onclick', onClick as EventListener);
});

onBeforeUnmount(() => {
  window.removeEventListener('onclick', onClick as EventListener);
});
</script>
