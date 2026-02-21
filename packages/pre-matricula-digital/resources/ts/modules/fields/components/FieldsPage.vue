<template>
  <main>
    <h1>Campos</h1>
    <skeleton-form-list v-if="loadingData" class="mt-5" :items="20" />
    <div v-if="!loadingData" class="row mt-5">
      <div class="col-12 col-md-6">
        <div class="row">
          <div class="col-12">
            <p>Dados do(a) responsável pelo(a) aluno(a)</p>
            <hr />
          </div>
          <div
            v-for="responsibleField in responsible"
            :key="(responsibleField.id as string)"
            class="col-12 col-sm-6 col-md-12 col-lg-6 mb-3"
            data-test="wrapper-checkbox"
            @click="openModalToUpdate(responsibleField)"
          >
            <label class="toggle-checkbox">
              <input type="checkbox" disabled :value="responsibleField.id" />
              <div
                :class="{ 'toggle-required': responsibleField.required }"
                class="toggle-text"
              >
                {{ responsibleField.name }}
              </div>
            </label>
          </div>
          <div v-if="responsible.length" class="col-12 mb-3">
            <label class="toggle-checkbox">
              <input type="checkbox" disabled />
              <div class="toggle-text toggle-required">Endereço</div>
            </label>
          </div>
          <div class="col-12">
            <x-btn
              data-test="btn-add-field-responsible"
              label="Adicionar novo campo"
              no-caps
              no-wrap
              color="primary"
              class="w-100"
              icon="mdi-plus"
              size="lg"
              @click="addField('RESPONSIBLE')"
            />
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 mt-5 mt-md-0">
        <div class="row">
          <div class="col-12">
            <p>Dados do(a) aluno(a)</p>
            <hr />
          </div>
          <div
            v-for="studentField in student"
            :key="(studentField.id as string)"
            class="col-12 col-sm-6 col-md-12 col-lg-6 mb-3"
            @click="openModalToUpdate(studentField)"
          >
            <label class="toggle-checkbox">
              <input type="checkbox" disabled :value="studentField.id" />
              <div
                :class="{ 'toggle-required': studentField.required }"
                class="toggle-text"
              >
                {{ studentField.name }}
              </div>
            </label>
          </div>
          <div class="col-12">
            <x-btn
              data-test="btn-add-field-student"
              label="Adicionar novo campo"
              no-caps
              no-wrap
              color="primary"
              class="w-100"
              icon="mdi-plus"
              size="lg"
              @click="addField('STUDENT')"
            />
          </div>
        </div>
      </div>
    </div>
    <modal
      ref="fieldsModal"
      v-model="open"
      no-footer
      :title="(!!field.id ? 'Editar ' : 'Novo ') + 'Campo'"
    >
      <template #body>
        <x-form
          v-if="open"
          :loading="loading"
          :schema="schema"
          :initial-values="{ ...field }"
          @submit="saveField"
          @open:options="handleOpenOptions"
          @close:options="handleCloseOptions"
        >
          <template #default="{ values, errors, setValues }">
            <div
              v-if="
                values.type === 'SELECT' ||
                values.type === 'MULTI_SELECT' ||
                values.type === 'RADIO'
              "
              class="col-12"
            >
              <h3>Opções</h3>
              <div
                v-for="(item, i) in values.options"
                :key="i"
                class="d-flex justify-content-between"
              >
                <div class="w-100 mr-2">
                  <x-field
                    v-model="item.name"
                    :name="`options[${i}].name`"
                    container-class="form-group"
                    type="TEXT"
                    rules="required"
                    :errors="!!errors[`options[${i}].name`]"
                  />
                </div>
                <div class="mr-2">
                  <x-field
                    v-model="item.weight"
                    :name="`options[${i}].weight`"
                    container-class="form-group"
                    type="TEXT"
                    rules="required|numeric"
                    :errors="!!errors[`options[${i}].weight`]"
                  />
                </div>
                <div class="d-flex align-items-start">
                  <x-btn
                    v-tooltip.start-bottom="'Remover Opção'"
                    no-caps
                    no-wrap
                    class="w-100 h-75 flex-row text-danger border-danger"
                    icon="mdi-trash-can-outline"
                    @click="removeOption(values, i, setValues)"
                  />
                </div>
              </div>
              <x-btn
                no-caps
                no-wrap
                outline
                color="primary"
                class="w-100 mb-4"
                label="Adicionar opção"
                @click="addOption(values, setValues)"
              />
            </div>
          </template>
        </x-form>
      </template>
    </modal>
  </main>
</template>

<script setup lang="ts">
import { AppContext, computed, getCurrentInstance, onMounted, ref } from 'vue';
import {
  ErrorResponse,
  FieldPage,
  FieldPageOption,
  SetValues,
} from '@/modules/fields/types';
import { useLoader, useLoaderAndShowErrorByModal } from '@/composables';
import { Field } from '@/modules/fields/api';
import { FormSchema } from '@/types';
import Modal from '@/components/elements/Modal.vue';
import SkeletonFormList from '@/components/loaders/components/FormList.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XField from '@/components/x-form/XField.vue';
import XForm from '@/components/x-form/XForm.vue';
import { fieldTypes } from '@/util';

const {
  loader: loaderData,
  data: fields,
  loading: loadingData,
} = useLoader<FieldPage[]>([]);

const loaderFieldError = ref();

const appContext = getCurrentInstance()?.appContext;

const { loader: loaderField, loading } = useLoaderAndShowErrorByModal<{
  errors?: ErrorResponse;
}>(appContext as AppContext, loaderFieldError);

const fieldsModal = ref<typeof Modal>();

const newOption = ref({
  name: null,
  weight: 0,
});

const newField = ref<FieldPage>({
  id: null,
  name: null,
  description: null,
  group: null,
  type: null,
  internal: false,
  options: [],
  deleteOptions: [],
});

const field = ref<FieldPage>({
  id: null,
  name: null,
  description: null,
  group: null,
  type: null,
  internal: false,
  options: [],
  deleteOptions: [],
});

const open = ref(false);
const types = ref(fieldTypes());

const responsible = computed(() =>
  fields.value.filter((f) => f.group === 'RESPONSIBLE')
);

const student = computed(() =>
  fields.value.filter((f) => f.group === 'STUDENT')
);

const schema = computed<FormSchema>(() => ({
  fields: [
    {
      label: 'Título da pergunta',
      name: 'name',
      type: 'TEXT',
      containerClass: 'col-12',
      rules: 'required',
    },
    {
      label: 'Descrição da pergunta',
      name: 'description',
      type: 'TEXT',
      containerClass: 'col-12',
      rules: '',
    },
    {
      label: 'Tipo da resposta',
      name: 'type',
      type: 'SELECT',
      options: types.value,
      containerClass: 'col-12',
      disabled: field.value.id ? true : undefined,
      rules: 'required',
      searchable: false,
    },
  ],
  buttons: [
    {
      type: 'submit',
      label: 'Salvar',
      class: 'btn btn-block btn-primary',
      containerClass: '',
      block: true,
      bind: {
        'data-test': 'btn-save-field',
      },
    },
  ],
  buttonsContainer: {
    class: '',
  },
}));

const addField = (group: 'RESPONSIBLE' | 'STUDENT') => {
  field.value = {
    ...newField.value,
    group,
    options: [],
    type: 'TEXT',
  };
  open.value = true;
};

const addOption = (values: FieldPage, setValues: SetValues) => {
  const { options } = values;

  let opts: FieldPageOption[] = [{ ...newOption.value }];

  if (options) {
    options.push({ ...newOption.value });
    opts = options;
  }

  setValues({
    ...values,
    options: opts,
  });
};

const openModalToUpdate = (item: FieldPage) => {
  field.value = { ...newField.value, ...item };
  open.value = true;
};

const createField = (model: FieldPage) => {
  loaderFieldError.value = {
    title: 'Erro ao criar o campo',
    description:
      'Aconteceu um erro ao criar o campo, por favor tente novamente em alguns instantes.',
  };
  loaderField(() => Field.post(model)).then(() => {
    open.value = false;
    loaderData(Field.list);
  });
};

const updateField = (model: FieldPage) => {
  loaderFieldError.value = {
    title: 'Erro ao editar o campo',
    description:
      'Aconteceu um erro ao editar o campo, por favor tente novamente em alguns instantes.',
  };
  loaderField(() => Field.put(model)).then(() => {
    open.value = false;
    loaderData(Field.list);
  });

  loading.value = true;
};

const saveField = (model: FieldPage) => {
  // Este trecho será reponsável por forçar o campo "weight" a ser um número
  if (model.options) {
    model.options.forEach((option) => {
      const opt = option;
      opt.weight = Number(option.weight);
    });
  }

  if (model.id) {
    updateField(model);
  } else {
    createField(model);
  }
};

const removeOption = (
  values: FieldPage,
  index: number,
  setValues: SetValues
) => {
  const option = values.options[index];
  const { options, deleteOptions } = values;

  options.splice(index, 1);
  if (option.id) {
    deleteOptions?.push(option.id);
  }

  setValues({
    ...values,
    options,
    deleteOptions,
  });
};

const handleOpenOptions = () => {
  if (field.value.options.length < 2 && fieldsModal.value) {
    fieldsModal.value?.setOverflowVisible(true);
  }
};

const handleCloseOptions = () => {
  if (fieldsModal.value) {
    fieldsModal.value?.setOverflowVisible(false);
  }
};

onMounted(() => loaderData(Field.list));
</script>
