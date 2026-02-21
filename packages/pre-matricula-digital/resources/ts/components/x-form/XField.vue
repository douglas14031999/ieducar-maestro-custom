<template>
  <Field
    v-if="type !== 'CHECKBOX'"
    v-slot="{ field, errors: errs }"
    ref="validator"
    v-model="model"
    as="div"
    class="form-group"
    :rules="Boolean(rules) ? rules : undefined"
    :class="containerClass"
    :name="name"
    :validate-on-input="errors"
    v-bind="$attrs"
  >
    <template v-if="!!label">
      <label
        :for="name"
        :class="[
          labelClass,
          {
            'text-danger': errors,
            required: Boolean(rules) ? rules.includes('required') : false,
          },
        ]"
        class="form-label"
        v-html="label"
      >
      </label>
    </template>

    <template v-if="type === 'TEXT' && !Boolean(mask)">
      <input
        v-bind="field"
        type="text"
        class="form-control"
        :class="{ 'is-invalid': errors }"
        :disabled="disabled"
        :placeholder="placeholder"
        :autocomplete="unique"
        @blur="$emit('blur')"
      />
      <div v-if="loading && !errors" class="input_spinner">
        <x-spinner color="primary" :normal="true" />
      </div>
    </template>

    <template v-if="type === 'TEXT' && Boolean(mask)">
      <input
        v-mask="mask"
        v-bind="field"
        type="text"
        class="form-control"
        :class="{ 'is-invalid': errors }"
        :disabled="disabled"
        :placeholder="placeholder"
        :autocomplete="unique"
      />
    </template>

    <template v-if="type === 'NUMBER'">
      <input
        v-bind="field"
        type="number"
        class="form-control"
        :class="{ 'is-invalid': errors }"
        :disabled="disabled"
        :placeholder="placeholder"
        :autocomplete="unique"
      />
    </template>

    <template v-if="type === 'LONG_TEXT'">
      <textarea
        v-bind="field"
        v-model="modelTextArea"
        class="form-control"
        rows="4"
        :class="{ 'is-invalid': errors }"
        :disabled="disabled"
        :placeholder="placeholder"
      ></textarea>
    </template>

    <template v-if="type === 'RICH_TEXT'">
      <input-froala
        v-model:data="model"
        v-bind="field"
        :class="{ 'is-invalid': errors }"
        :disabled="disabled"
      />
    </template>

    <template v-if="type === 'EMAIL'">
      <input
        v-bind="field"
        type="email"
        class="form-control"
        :class="{ 'is-invalid': errors }"
        :disabled="disabled"
        :placeholder="placeholder"
        :autocomplete="unique"
        @keydown="handleKeydownEmail"
      />
    </template>

    <template v-if="type === 'DATE'">
      <input-date
        :unique="unique"
        :placeholder="placeholder"
        v-bind="field"
        :errors="errors"
      />
    </template>

    <template v-if="type === 'TIME'">
      <input-hour
        v-model="model"
        :unique="unique"
        :placeholder="placeholder"
        v-bind="field"
        :class="{ 'is-invalid': errors }"
      />
    </template>

    <template v-if="type === 'CPF'">
      <input-cpf
        :unique="unique"
        :placeholder="placeholder"
        v-bind="field"
        :class="{ 'is-invalid': errors }"
        class="form-control"
      />
    </template>

    <template v-if="type === 'RG'">
      <input-rg
        :id="name"
        :unique="unique"
        :placeholder="placeholder"
        v-bind="field"
        class="form-control"
        :class="{ 'is-invalid': errors }"
      />
    </template>

    <template v-if="type === 'BIRTH_CERTIFICATE'">
      <input-birth-certificate
        :unique="unique"
        v-bind="field"
        class="form-control"
        :class="{ 'is-invalid': errors }"
        :placeholder="placeholder"
      />
    </template>

    <template v-if="type === 'PHONE'">
      <input-phone
        v-model:data="model"
        :unique="unique"
        v-bind="field"
        class="form-control"
        :class="{ 'is-invalid': errors }"
        :placeholder="placeholder"
      />
    </template>

    <template v-if="type === 'RADIO'">
      <div
        v-for="(option, index) in options"
        :key="(option.key as string)"
        class="custom-control custom-radio mb-3"
      >
        <input
          v-bind="field"
          :id="`option_${name}_${index}_${option.key}`"
          :name="name"
          :value="option.key"
          type="radio"
          :checked="option.key === model"
          class="custom-control-input"
        />
        <label
          :for="`option_${name}_${index}_${option.key}`"
          class="custom-control-label"
          v-html="option.label"
        >
        </label>
        <div v-if="!!option.subLabel">
          <small class="text-muted">
            {{ option.subLabel }}
          </small>
        </div>
      </div>
    </template>

    <template v-if="type === 'PRETTY_RADIO'">
      <label
        v-for="option in computedOptions"
        :key="(option.key as string)"
        class="toggle-radio"
      >
        <input v-bind="field" type="radio" :name="name" :value="option.key" />
        <span class="d-block toggle-text">
          {{ option.label }}
        </span>
      </label>
    </template>

    <template v-if="type === 'SELECT'">
      <x-select
        v-bind="field"
        v-model:data="model"
        :options="computedOptions"
        :disabled="disabled"
        :class="{ 'is-invalid': errors }"
        :placeholder="placeholder || undefined"
        :clearable="allowClear"
        :searchable="searchable"
        :unique="unique"
        :error="errors"
        @change="$emit('change', model)"
      />
    </template>

    <template v-if="type === 'MULTISELECT'">
      <multi-select-custom
        v-model="model"
        :unique="unique"
        :error="!!errors"
        :options="computedOptions"
        :placeholder="placeholder || undefined"
        @change="$emit('change', model)"
      />
    </template>

    <template v-if="type === 'GENDER'">
      <x-select
        v-bind="field"
        v-model:data="model"
        :options="genders"
        :disabled="disabled"
        :class="{ 'is-invalid': errors }"
        :placeholder="placeholder"
        :clearable="allowClear"
        :searchable="searchable"
        :unique="unique"
        :error="errors"
      />
    </template>

    <template v-if="type === 'MARITAL_STATUS'">
      <x-select
        v-bind="field"
        v-model:data="model"
        :options="maritalStatuses"
        :disabled="disabled"
        :class="{ 'is-invalid': errors }"
        :placeholder="placeholder"
        :clearable="allowClear"
        :searchable="searchable"
        :unique="unique"
        :error="errors"
      />
    </template>

    <template v-if="type === 'CITY'">
      <x-select-cities
        v-bind="field"
        v-model:data="model"
        :disabled="disabled"
        :class="{ 'is-invalid': errors }"
        :placeholder="placeholder"
        :unique="unique"
        :error="errors"
      />
    </template>

    <template v-if="type === 'CEP'">
      <input-postal-code
        v-bind="field"
        :cep="model"
        :placeholder="placeholder"
        :unique="unique"
        @change:address="$emit('change:address', $event)"
      />
    </template>

    <template v-if="type === 'FILE'">
      <div class="input-group mb-3">
        <div class="custom-file">
          <input :id="name" type="file" class="custom-file-input" />
          <label class="custom-file-label" :for="name">
            {{ nameFromFile(field) }}
          </label>
        </div>
      </div>
    </template>

    <template v-if="!!description && errs.length === 0">
      <div>
        <label :for="name" class="form-label text-muted" v-html="description">
        </label>
      </div>
    </template>

    <ErrorMessage :name="name" class="validator-rule-error" />
  </Field>

  <div v-else :class="containerClass" class="mb-3">
    <div class="custom-control custom-checkbox">
      <Field
        v-slot="{ errors: errs }"
        v-model="modelCheckbox"
        :name="name"
        :value="model"
        :unchecked-value="false"
        :rules="Boolean(rules) ? rules : undefined"
        type="checkbox"
      >
        <input
          :id="name"
          v-model="modelCheckbox"
          class="custom-control-input"
          type="checkbox"
          :name="name"
          :value="model"
        />
        <label :for="name" class="custom-control-label">
          {{ label }}
        </label>
        <div v-if="!!subLabel">
          <small class="text-muted">
            {{ subLabel }}
          </small>
        </div>
        <div v-else-if="!!description && errs.length === 0">
          <small class="text-muted">
            {{ description }}
          </small>
        </div>
      </Field>
      <div>
        <ErrorMessage :name="name" class="validator-rule-error" />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { ErrorMessage, Field } from 'vee-validate';
import { ModelValue, Option } from '@/types';
import {
  PropType,
  WritableComputedRef,
  computed,
  defineComponent,
  ref,
} from 'vue';
import { genders, maritalStatuses } from '@/util';
import InputBirthCertificate from '@/components/form/InputBirthCertificate.vue';
import InputCpf from '@/components/form/InputCpf.vue';
import InputDate from '@/components/form/InputDate.vue';
import InputFroala from '@/components/form/InputFroala.vue';
import InputHour from '@/components/form/InputHour.vue';
import InputPhone from '@/components/form/InputPhone.vue';
import InputPostalCode from '@/components/form/InputPostalCode.vue';
import InputRg from '@/components/form/InputRg.vue';
import MultiSelectCustom from '@/components/form/MultiSelectCustom.vue';
import XSelect from '@/components/form/XSelect.vue';
import XSelectCities from '@/components/form/XSelectCities.vue';
import XSpinner from '@/components/loaders/XSpinner.vue';
import { mask } from 'vue-the-mask';
import { useVModel } from '@vueuse/core';
export default defineComponent({
  components: {
    Field,
    ErrorMessage,
    InputDate,
    InputBirthCertificate,
    InputHour,
    InputCpf,
    InputRg,
    InputPhone,
    InputPostalCode,
    InputFroala,
    MultiSelectCustom,
    XSpinner,
    XSelect,
    XSelectCities,
  },
  directives: {
    mask,
  },
  props: {
    modelValue: {
      type: [String, Number, Boolean, Array] as PropType<ModelValue>,
      default: undefined,
    },
    containerClass: {
      type: [String, Object],
      default: 'col-8 offset-2',
    },
    type: {
      type: String,
      default: 'TEXT',
    },
    options: {
      type: Array as PropType<Option[]>,
      default: () => [],
    },
    name: {
      type: String,
      default: '',
    },
    rules: {
      type: [String, Object],
      default: null,
    },
    label: {
      type: String,
      default: null,
    },
    description: {
      type: String,
      default: null,
    },
    labelClass: {
      type: String,
      default: null,
    },
    subLabel: {
      type: String,
      default: null,
    },
    errors: Boolean,
    disabled: Boolean,
    placeholder: {
      type: String,
      default: undefined,
    },
    bind: {
      type: Object,
      default: () => ({}),
    },
    mask: {
      type: String,
      default: null,
    },
    allowClear: Boolean,
    searchable: {
      type: Boolean,
      default: false,
    },
    loading: Boolean,
  },
  emits: [
    'change:address',
    'close:options',
    'open:options',
    'blur',
    'update:modelValue',
    'change',
  ],
  setup(props) {
    const model = useVModel(props, 'modelValue');
    const modelCheckbox = useVModel(
      props,
      'modelValue'
    ) as WritableComputedRef<boolean>;
    const modelTextArea = useVModel(props, 'modelValue') as WritableComputedRef<
      string | number | string[] | undefined
    >;

    const computedOptions = computed(() => props.options);

    const unique = ref(Math.random().toString(36).substring(2, 9));

    const handleKeydownEmail = (el: KeyboardEvent) => {
      if (el.key === ' ') {
        el.preventDefault();
      }
    };

    const nameFromFile = function (field: unknown) {
      const newField = field as { value?: { name: string } };

      if (newField.value) {
        return newField.value?.name;
      }

      return 'Selecione';
    };

    return {
      nameFromFile,
      genders: genders(),
      maritalStatuses: maritalStatuses(),
      model,
      modelCheckbox,
      modelTextArea,
      unique,
      handleKeydownEmail,
      computedOptions,
    };
  },
});
</script>
