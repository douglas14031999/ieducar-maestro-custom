<template>
  <modal
    v-model="model"
    :title="
      !noGradesSuggested
        ? 'Deseja continuar na lista?'
        : 'Não é possível continuar na lista'
    "
    title-class="font-hind-18-primary text-primary"
  >
    <template #body>
      <suggested-grades-message-component
        v-if="
          preregistration.process.blockIncompatibleAgeGroup ||
          preregistration.process.forceSuggestedGrade
        "
        :suggested-grades="suggestedGrades"
        :suggested-grades-message="suggestedGradesMessage"
      />
      <no-suggested-grades-message :no-grades-suggested="noGradesSuggested" />
      <div v-if="!noGradesSuggested" class="mb-4">
        Para confirmar a permanência na lista de espera, clique em prosseguir.
      </div>
    </template>
    <template #footer>
      <div class="row p-2">
        <div v-if="!noGradesSuggested" class="col-6">
          <x-btn
            color="gray-500"
            outline
            class="w-100"
            label="Voltar"
            no-caps
            no-wrap
            @click="closeModalMessage"
          />
        </div>
        <div v-if="!noGradesSuggested" class="col-6">
          <x-btn
            :loading="loadingReturnToWait"
            color="primary"
            class="w-100 border-primary"
            label="Prosseguir"
            no-caps
            no-wrap
            @click="returnToWait"
          />
        </div>
        <div v-else class="col-12">
          <x-btn
            color="primary"
            class="w-100 border-primary"
            label="Entendi"
            no-caps
            no-wrap
            @click="closeModalMessage"
          />
        </div>
      </div>
    </template>
  </modal>
</template>

<script setup lang="ts">
import {
  ProtocolStatusPreRegistration,
  ProtocolStatusReturnToWait,
} from '@/modules/protocol/types';
import {
  useLoaderAndThrowError,
  useModal,
  useStudentProcessAndSuggestGrades,
} from '@/composables';
import Modal from '@/components/elements/Modal.vue';
import NoSuggestedGradesMessage from '@/modules/preregistration/components/NoSuggestedGradesMessage.vue';
import { PreRegistrationStageProcess } from '@/modules/preregistration/types';
import { Protocol } from '@/modules/protocol/api';
import SuggestedGradesMessageComponent from '@/modules/preregistration/components/SuggestedGradesMessage.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import { computed } from 'vue';
import { useVModel } from '@vueuse/core';

const emit = defineEmits<{
  (event: 'get-protocol'): void;
  (event: 'update:modelValue'): void;
}>();

const props = defineProps<{
  modelValue: boolean;
  preregistration: ProtocolStatusPreRegistration;
}>();

const { dialog } = useModal();

const process = computed(() => {
  return props.preregistration
    .process as unknown as PreRegistrationStageProcess;
});

const { suggestedGrades, suggestGrade, suggestedGradesMessage } =
  useStudentProcessAndSuggestGrades(process);

const noGradesSuggested = computed(
  () =>
    process.value.blockIncompatibleAgeGroup &&
    suggestedGrades.value.length < 1 &&
    !!props.preregistration.student.dateOfBirth
);

const { loader: loaderReturnToWait, loading: loadingReturnToWait } =
  useLoaderAndThrowError<ProtocolStatusReturnToWait>();

const model = useVModel(props, 'modelValue');

const closeModalMessage = () => {
  model.value = false;
  emit('get-protocol');
};

const ctx = {
  dialog,
};

const returnToWait = () => {
  const gradeId = suggestedGrades.value[0]?.id ?? null;

  loaderReturnToWait(() =>
    Protocol.postReturnToWait(
      {
        id: props.preregistration?.id as string,
      },
      gradeId
    )
  )
    .then(() => {
      ctx.dialog({
        title: 'Sucesso!',
        description:
          'A permanência na lista de espera foi confirmada com sucesso.',
      });
    })
    .catch((err) => {
      if (err.response.data.errors) {
        ctx.dialog({
          title: 'Erro!',
          description:
            'Não foi possível confirmar a permanência na lista de espera. ' +
            'Por favor, entre em contato com o suporte.',
          titleClass: 'danger',
        });
      }
    })
    .finally(() => {
      closeModalMessage();
    });
};

suggestGrade(props.preregistration.student.dateOfBirth);
</script>
