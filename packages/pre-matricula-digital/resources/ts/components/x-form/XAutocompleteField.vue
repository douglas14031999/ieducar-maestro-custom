<template>
  <div ref="autocompleteRef" class="autocomplete-field-wrapper">
    <label v-if="label" :for="name" class="autocomplete-label">{{
      label
    }}</label>
    <input
      :id="name"
      v-model="inputValue"
      :name="name"
      :placeholder="placeholder"
      :disabled="disabled"
      class="autocomplete-input"
      type="text"
      autocomplete="off"
      @input="onInput"
      @focus="onFocus"
      @blur="onBlur"
    />
    <small v-if="subDescription" class="text-muted d-block mt-1">{{
      subDescription
    }}</small>
    <ul
      v-if="searchable && showSuggestions"
      class="autocomplete-suggestions-fix"
    >
      <li v-if="loading" class="autocomplete-feedback">
        Carregando sugestões...
      </li>
      <template v-else>
        <li
          v-if="filteredSuggestions.length === 0"
          class="autocomplete-feedback"
        >
          Nenhum resultado encontrado
        </li>
        <li
          v-for="suggestion in filteredSuggestions"
          :key="suggestion.id"
          @mousedown.prevent="selectSuggestion(suggestion)"
        >
          {{ suggestion.name }}
          <small v-if="suggestion.cpf" class="text-muted d-block">
            CPF: {{ formatCpf(suggestion.cpf) }}
          </small>
          <small v-if="suggestion.dateOfBirth" class="text-muted d-block">
            Nasc: {{ formatDate(suggestion.dateOfBirth) }}
          </small>
        </li>
      </template>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { formatCpf, formatDate } from '@/utils/formatters';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  searchable: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  subDescription: {
    type: String,
    default: '',
  },
  name: {
    type: String,
    default: '',
  },
  rules: {
    type: [String, Object],
    default: '',
  },
  options: {
    type: Array as () => string[],
    default: () => [],
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  fetchSuggestions: {
    type: Function,
    required: false,
    default: async () => null,
  },
});

const emit = defineEmits(['update:modelValue', 'select']);
const inputValue = ref(props.modelValue);
const showSuggestions = ref(false);
const loading = ref(false);
type Suggestion = {
  id: string;
  name: string;
  date_of_birth?: string;
  cpf?: string;
  [key: string]: unknown;
};
const suggestions = ref<Suggestion[]>([]);
let debounceTimeout: ReturnType<typeof setTimeout> | null = null;
let lastSearchedTerm = '';
let lastNoResults = false;
const autocompleteRef = ref<HTMLElement | null>(null);
const locked = ref(false);

watch(
  () => props.modelValue,
  (val) => {
    inputValue.value = val;
    if (locked.value && val !== lastSearchedTerm) {
      locked.value = false;
    }
  }
);

function onInput(e: Event) {
  const value = (e.target as HTMLInputElement).value;
  emit('update:modelValue', value);
  if (locked.value && value !== lastSearchedTerm) {
    locked.value = false;
  }
  if (!props.searchable) return;
  if (locked.value) return;
  if (debounceTimeout) clearTimeout(debounceTimeout);

  if (
    lastNoResults &&
    value.length > lastSearchedTerm.length &&
    value.startsWith(lastSearchedTerm)
  ) {
    suggestions.value = [];
    showSuggestions.value = true;
    loading.value = false;
    return;
  }

  if (value.length >= 3 && typeof props.fetchSuggestions === 'function') {
    loading.value = true;
    showSuggestions.value = true;
    debounceTimeout = setTimeout(async () => {
      try {
        const results = await props.fetchSuggestions(value);
        suggestions.value = Array.isArray(results)
          ? results.map((s: string | Suggestion) =>
              typeof s === 'string' ? { id: s, name: s } : s
            )
          : [];
        showSuggestions.value = true;
        lastSearchedTerm = value;
        lastNoResults = suggestions.value.length === 0;
      } catch (error) {
        suggestions.value = [];
        showSuggestions.value = false;
        lastNoResults = true;
      } finally {
        loading.value = false;
      }
    }, 300);
  } else {
    suggestions.value = [];
    showSuggestions.value = false;
    loading.value = false;
    lastSearchedTerm = '';
    lastNoResults = false;
  }
}

function onFocus() {
  if (!props.searchable) return;
  if (locked.value) return;
  if (inputValue.value.length >= 3 && suggestions.value.length > 0) {
    showSuggestions.value = true;
  }
}

function onBlur() {
  setTimeout(() => {
    showSuggestions.value = false;
  }, 120);
}

function selectSuggestion(suggestion: Suggestion) {
  inputValue.value = suggestion.name;
  emit('update:modelValue', suggestion.name);
  emit('select', suggestion); // Emite o objeto completo!
  showSuggestions.value = false;
  lastSearchedTerm = suggestion.name;
  locked.value = true; // trava o autocomplete até o valor ser alterado
}

const filteredSuggestions = computed(() => {
  const value = inputValue.value || '';
  if (typeof props.fetchSuggestions === 'function') {
    // Quando busca remota, já filtra no backend
    return suggestions.value;
  }
  if (value.length < 3) return [];
  // Garante que todas as opções locais são objetos { id, name }
  return props.options
    .filter((s) => s.toLowerCase().includes(value.toLowerCase()))
    .slice(0, 10)
    .map((s) => ({ id: s, name: s } as Suggestion));
});

function handleClickOutside(event: MouseEvent) {
  if (
    autocompleteRef.value &&
    !autocompleteRef.value.contains(event.target as Node)
  ) {
    showSuggestions.value = false;
  }
}
function handleEscKey(event: KeyboardEvent) {
  if (event.key === 'Escape') {
    showSuggestions.value = false;
  }
}

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside);
  document.addEventListener('keydown', handleEscKey);
});
onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside);
  document.removeEventListener('keydown', handleEscKey);
});
</script>

<style scoped>
.autocomplete-field-wrapper {
  width: 100%;
  position: relative;
}
.autocomplete-label {
  display: block;
  margin-bottom: 4px;
  font-size: 0.98rem;
  color: #2c3e50;
  font-weight: 500;
}
.autocomplete-input {
  width: 100%;
  padding: 10px 12px;
  border: 1.5px solid #b3c6e0;
  border-radius: 8px;
  font-size: 1.08rem;
  color: #2c3e50;
  outline: none;
  transition: border 0.2s;
}
.autocomplete-input:focus {
  border-color: #1976d2;
  box-shadow: 0 0 0 2px #eaf3ff;
}
.autocomplete-suggestions-fix {
  position: absolute;
  left: 0;
  top: 100%;
  width: 100%;
  z-index: 20;
  background: #fff;
  border: 1.5px solid #b3c6e0;
  border-radius: 12px;
  max-height: 220px;
  overflow-y: auto;
  margin-top: 2px;
  box-shadow: 0 4px 24px rgba(44, 62, 80, 0.1);
  list-style: none;
  padding: 0;
  font-size: 1rem;
}
.autocomplete-suggestions-fix li {
  padding: 8px 14px;
  cursor: pointer;
  transition: background 0.2s;
  border-bottom: 1px solid #f0f4fa;
  color: #2c3e50;
}
.autocomplete-suggestions-fix li:last-child {
  border-bottom: none;
}
.autocomplete-suggestions-fix li:hover {
  background: #eaf3ff;
  color: #1976d2;
  font-weight: 500;
}
.autocomplete-feedback {
  color: #6c757d;
  padding: 8px 14px;
  text-align: left;
  font-size: 0.89rem;
  cursor: default;
  user-select: none;
  font-weight: normal;
}
</style>
