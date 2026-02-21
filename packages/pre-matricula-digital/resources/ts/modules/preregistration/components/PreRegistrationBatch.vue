<template>
  <modal
    v-model="openModal"
    :large="true"
    :title="getStepTitle"
    :overflow-visible="!success && step === 'ACCEPT'"
    no-footer
  >
    <template #body>
      <div v-if="preregistrations && success === false">
        <div class="m-auto" style="max-width: 740px">
          <div
            v-if="step === 'REJECT'"
            class="mt-4 text-danger font-weight-bold"
          >
            Tem certeza que deseja indeferir as pré-matrículas?
            <hr />
          </div>
          <div v-if="step === 'ACCEPT'" class="mt-4 font-weight-bold">
            Selecione a turma que deseja matricular os(as) alunos(as) para
            completar o deferimento das pré-matrículas dos(as) alunos(as).
            <hr />
            <x-field
              v-model="classroom"
              container-class="col-12 form-group mt-3"
              rules="required"
              label="Para qual turma deseja designar os(as) alunos(as)?"
              name="classroom"
              type="SELECT"
              :errors="showError"
              :options="classrooms"
            />
          </div>
          <div v-if="step === 'SUMMON'" class="mt-4 font-weight-bold">
            Tem certeza que deseja convocar os responsáveis desta(s)
            pré-matrícula(s)?
            <hr />
          </div>
          <div v-if="step === 'SUMMON'" class="form-group">
            <label class="form-label" for="summon-justification"
              >Observações (opcional)</label
            >
            <textarea
              id="summon-justification"
              v-model="justification"
              class="form-control"
              rows="3"
            ></textarea>
          </div>
          <div v-if="step === 'REJECT'" class="form-group">
            <label class="form-label" for="reject-justification"
              >Justificativa (opcional)</label
            >
            <textarea
              id="reject-justification"
              v-model="justification"
              class="form-control"
              rows="3"
            ></textarea>
          </div>
          <div v-if="showError">
            <h2 class="text-danger">
              {{ errorTitle }}
            </h2>
            <span v-html="error"></span>
          </div>
          <div class="d-flex justify-content-center mt-4">
            <x-btn
              v-if="step === 'SUMMON'"
              label="Convocar responsáveis"
              :loading="loading"
              color="summon"
              outline
              class="ml-3 pmd-custom"
              icon="pmd-summon"
              no-caps
              no-wrap
              loading-normal
              @click="summon"
            />
            <x-btn
              v-if="step === 'REJECT'"
              label="Indeferir pré-matrículas"
              :loading="loading"
              color="rejected"
              outline
              class="ml-3 pmd-custom"
              icon="pmd-rejected"
              no-caps
              no-wrap
              loading-normal
              @click="reject"
            />
            <x-btn
              v-if="step === 'ACCEPT'"
              label="Deferir pré-matrículas"
              :loading="loading"
              color="accepted"
              outline
              class="ml-3 pmd-custom"
              icon="pmd-accepted"
              no-caps
              no-wrap
              loading-normal
              @click="accept"
            />
          </div>
        </div>
      </div>
      <div v-else-if="success === false">Carregando..</div>
      <div v-if="preregistrations && success === true">
        <div v-if="step === 'ACCEPT'" class="mt-4 font-weight-bold">
          {{ total }} matrícula(s) deferida(s) com sucesso
        </div>
        <div v-if="step === 'REJECT'" class="mt-4 font-weight-bold">
          {{ total }} matrícula(s) indeferida(s) com sucesso
        </div>
        <div v-if="step === 'SUMMON'" class="mt-4 font-weight-bold">
          {{ total }} matrícula(s) convocada(s) com sucesso
        </div>
        <div v-if="showError" class="mt-4">
          <h2 class="text-danger">
            {{ errorTitle }}
          </h2>
          <span v-html="error"></span>
        </div>
        <div class="row justify-content-center d-flex mt-4">
          <x-btn
            class="ml-3 mr-3 w-50"
            color="success"
            label="Fechar"
            no-caps
            no-wrap
            @click="closeModal"
          />
        </div>
      </div>
    </template>
  </modal>
</template>

<script setup lang="ts">
import { ErrorResponse, Nullable, Option } from '@/types';
import {
  Filter,
  PreregistrationBatchAccept,
  PreregistrationBatchReject,
  PreregistrationBatchSummon,
} from '@/modules/preregistration/types';
import { computed, onMounted, ref } from 'vue';
import { useLoader, useLoaderAndThrowError } from '@/composables';
import Modal from '@/components/elements/Modal.vue';
import { Preregistration as PreregistrationApi } from '@/modules/preregistration/api';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XField from '@/components/x-form/XField.vue';
import { useVModel } from '@vueuse/core';

const emit = defineEmits<{
  (action: 'load'): void;
}>();

const props = defineProps<{
  filter: Filter;
  preregistrations: string[];
  step?: string;
  processYear: Nullable<number>;
  modelValue: boolean;
}>();

const { loader: loaderAccept } =
  useLoaderAndThrowError<PreregistrationBatchAccept>();

const { loader: loaderReject } =
  useLoaderAndThrowError<PreregistrationBatchReject>();

const { loader: loaderSummon } =
  useLoaderAndThrowError<PreregistrationBatchSummon>();

const { loader: loaderClassroomsByPreregistration, data: classrooms } =
  useLoader<Option[]>();

const justification = ref('');
const classroom = ref<number>();
const success = ref(false);
const showError = ref(false);
const errorTitle = ref();
const error = ref();
const loading = ref(false);
const total = ref(0);

const openModal = useVModel(props, 'modelValue');

const getStepTitle = computed(() => {
  switch (props.step) {
    case 'REJECT':
      return 'Indeferimento em lote';
    case 'ACCEPT':
      return 'Deferimento em lote';
    case 'SUMMON':
      return 'Convocação em lote';
    default:
      return '';
  }
});

onMounted(() => {
  if (openModal.value && props.step === 'ACCEPT') {
    getClassroomsByPreregistration();
  }
});

const close = () => {
  openModal.value = false;

  justification.value = '';
  classrooms.value = [];
  classroom.value = undefined;
  success.value = false;
  showError.value = false;
  errorTitle.value = null;
  error.value = null;
  loading.value = false;
  total.value = 0;
};

const closeModal = () => {
  close();
  emit('load');
};

const accept = () => {
  if (!Number(classroom.value)) {
    showError.value = true;
    errorTitle.value =
      'É necessário selecionar uma turma para realizar o Deferimento da pré-matrícula.';
    error.value = '';
    return;
  }

  loading.value = true;

  loaderAccept(() =>
    PreregistrationApi.postAcceptBatch({
      ids: props.preregistrations.map((id) => Number(id)),
      classroom: Number(classroom.value),
    })
  )
    .then(({ acceptPreRegistrations }) => {
      if (acceptPreRegistrations) {
        success.value = true;
        total.value = acceptPreRegistrations.length;
      }
    })
    .catch((err) => {
      if (err.response.data.errors && err.response.data.errors.length) {
        showError.value = true;
        success.value = true;
        errorTitle.value =
          'Não foi possível realizar o deferimento de todos os(as) alunos(as)';
        error.value = err.response.data.errors
          .map((e: ErrorResponse) => e.extensions?.message)
          .join('<br>');
        if (err.response.data.data.acceptPreRegistrations) {
          total.value = err.response.data.data.acceptPreRegistrations.length;
        }
      } else if (err.errors && err.errors.length) {
        showError.value = true;
        errorTitle.value =
          'Não foi possível realizar o deferimento de todos os(as) alunos(as)';
        error.value = err.errors
          .map((e: ErrorResponse) => e.extensions?.message)
          .join('<br>');
      }
    })
    .finally(() => {
      loading.value = false;
    });
};

const reject = () => {
  loading.value = true;

  loaderReject(() =>
    PreregistrationApi.postRejectBatch({
      ids: props.preregistrations,
      justification: justification.value,
    })
  )
    .then(({ rejectPreRegistrations }) => {
      if (rejectPreRegistrations) {
        success.value = true;
        total.value = rejectPreRegistrations.length;
      }
    })
    .catch((err) => {
      if (err.errors && err.errors.length) {
        showError.value = true;
        errorTitle.value = err.errors[0].message;
        error.value = err.errors
          .map((e: ErrorResponse) => e.extensions?.message)
          .join('<br>');
        return;
      }
    })
    .finally(() => {
      loading.value = false;
    });
};

const summon = () => {
  loading.value = true;

  loaderSummon(() =>
    PreregistrationApi.postSummonBatch({
      ids: props.preregistrations,
      justification: justification.value,
    })
  )
    .then(({ summonPreRegistrations }) => {
      if (summonPreRegistrations) {
        success.value = true;
        total.value = summonPreRegistrations.length;
      }
    })
    .catch((err) => {
      if (err.response.data.errors && err.response.data.errors.length) {
        showError.value = true;
        errorTitle.value =
          'Não foi possível realizar a convocação de todos os(as) responsáveis(as)';
        error.value = err.response.data.errors
          .map((e: ErrorResponse) => e.extensions?.message)
          .join('<br>');
      }
    })
    .finally(() => {
      loading.value = false;
    });
};

const getClassroomsByPreregistration = () => {
  classrooms.value = [];

  loaderClassroomsByPreregistration(() =>
    PreregistrationApi.getClassroomsByPreregistration({
      school: props.filter.school as string,
      grade: props.filter.grade as string,
      year: props.processYear as number,
    })
  );
};
</script>
