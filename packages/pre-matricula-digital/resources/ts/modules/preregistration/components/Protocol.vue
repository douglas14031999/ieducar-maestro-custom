<template>
  <div class="container-pmd">
    <h2 class="font-muli-20-primary mb-4 text-center">
      Sua inscrição foi realizada com sucesso!
    </h2>
    <p class="mb-4">
      Para acompanhar a atualização da Pré-matrícula, não esqueça de
      anotar/salvar o(s) código(s) do(s)
      <strong>protocolo(s)</strong> gerado(s).
    </p>
    <div class="m-auto" style="max-width: 340px">
      <p v-if="preregistrations.length === 1" class="font-weight-bold">
        Seu protocolo:
      </p>
      <p v-if="preregistrations.length > 1" class="font-weight-bold">
        Seus protocolos:
      </p>
      <div
        v-for="preregistration in preregistrations"
        :key="preregistration.id"
        class="mt-4"
      >
        <x-card bordered class="protocol m-auto">
          <x-card-section>
            <div v-if="preregistration.type === 'WAITING_LIST'">
              <div class="alert badge-yellow text-center text-uppercase">
                Lista de espera
              </div>
            </div>
            <div v-else>
              <div class="alert badge-blue text-center text-uppercase">
                Pré-matrícula
              </div>
            </div>
            <div
              class="pull-right text-right"
              style="width: 120px; height: 90px"
            >
              <img :src="store.logo" :alt="store.entity.city" height="90" />
            </div>
            <dl class="mb-0">
              <dt class="mt-3">Código do protocolo</dt>
              <dd class="font-hind-18-primary font-weight-bold">
                #{{ preregistration.protocol }}
              </dd>
              <pre-registration-position
                v-if="process.showPriorityProtocol"
                :preregistration="preregistration"
              />
              <dt class="mt-3">Data da solicitação</dt>
              <dd>{{ $filters.formatDateTime(preregistration.date) }}</dd>
              <dt class="mt-3">Nome do(a) aluno(a)</dt>
              <dd>{{ student.student_name }}</dd>
              <dt class="mt-3">Série/Turno</dt>
              <dd>
                {{ preregistration.grade.name }} /
                {{ preregistration.period.name }}
              </dd>
              <dt class="mt-3">Escola</dt>
              <dd>
                <p class="mb-0">
                  {{ preregistration.school.name }}
                </p>
                <p v-if="preregistration.school.phone" class="mb-0">
                  Telefone:
                  {{
                    `(${preregistration.school.area_code}) ${preregistration.school.phone}`
                  }}
                </p>
              </dd>
            </dl>
            <div class="text-muted text-size-10 mt-4">
              <span class="text-uppercase">Código de autenticidade:</span>
              <br />
              <span class="text-dark">
                {{ preregistration.code }}
              </span>
            </div>
          </x-card-section>
        </x-card>
      </div>
    </div>
    <div class="mt-5 text-center" v-html="modelProcess.messageFooter"></div>
    <div class="text-center mt-5 mb-4">
      <div class="row mb-5">
        <div class="col-12 col-md-4 offset-md-2">
          <x-btn
            color="primary"
            outline
            label="Imprimir"
            class="w-100 mb-2"
            size="lg"
            no-caps
            no-wrap
            @click="print"
          />
        </div>
        <div class="col-12 col-md-4">
          <x-btn
            color="primary"
            outline
            label="Enviar por e-mail"
            class="w-100"
            size="lg"
            no-caps
            no-wrap
            @click="openModalEmail"
          />
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-5 offset-md-1 mb-4">
          <x-btn
            color="primary"
            label="Nova Inscrição"
            outline
            class="w-100"
            size="lg"
            no-caps
            no-wrap
            @click="submit"
          />
        </div>
        <div class="col-12 col-md-5 mb-4">
          <x-btn
            color="primary"
            label="Voltar para a tela inicial"
            class="w-100 border-primary"
            size="lg"
            no-caps
            no-wrap
            @click="$router.push('/')"
          />
        </div>
      </div>
    </div>
    <modal-component
      v-model="modal.email"
      title="Enviar protocolo(s) por e-mail"
      no-footer
    >
      <template #body>
        <p>
          Preencha o campo abaixo com o e-mail que deve receber o(s)
          protocolo(s):
        </p>
        <x-form :schema="schema" :loading="loading" @submit="sendEmail" />
      </template>
    </modal-component>
  </div>
</template>

<script setup lang="ts">
import {
  PreRegistrationOverview,
  PreRegistrationResponsibleField,
  PreRegistrationStageProcess,
  PreRegistrationStudentField,
} from '../types';
import { computed, inject, ref } from 'vue';
import { useLoader, useModal } from '@/composables';
import { Filters } from '@/filters';
import { FormSchema } from '@/types';
import ModalComponent from '@/components/elements/Modal.vue';
import PreRegistrationPosition from '@/components/elements/PreRegistrationPosition.vue';
import { Preregistration as PreregistrationApi } from '@/modules/preregistration/api';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XForm from '@/components/x-form/XForm.vue';
import { useGeneralStore } from '@/store/general';
import { useVModel } from '@vueuse/core';

const $filters = inject('$filters') as Filters;

const emit = defineEmits<{
  (action: 'finish'): void;
}>();

const props = defineProps<{
  preregistrations: PreRegistrationOverview[];
  process: PreRegistrationStageProcess;
  student: PreRegistrationStudentField;
  responsible: PreRegistrationResponsibleField;
}>();

const { dialog } = useModal();

const { loader, loading } = useLoader<boolean>();

const store = useGeneralStore();

const email = ref<string>();
const modal = ref({
  email: false,
});

const modelProcess = useVModel(props, 'process');

const schema = computed<FormSchema>(
  () =>
    ({
      fields: [
        {
          type: 'EMAIL',
          label: '',
          name: 'email',
          rules: 'required|email',
          containerClass: 'col-12',
        },
      ],
      buttons: [
        {
          type: 'submit',
          label: 'Prosseguir',
          class: 'btn btn-block btn-primary',
          containerClass: 'mt-3',
          block: true,
        },
      ],
      buttonsContainer: {
        class: '',
      },
    } as FormSchema)
);

const print = () => {
  window.print();
};

const openModalEmail = () => {
  modal.value.email = true;
  email.value = props.responsible.responsible_email;
};

const sendEmail = (model: { email: string }) => {
  loader(() =>
    PreregistrationApi.postSendEmail({
      email: model.email,
      preregistrations: props.preregistrations.map((p) => p.id),
    })
  ).then((success) => {
    if (success) {
      modal.value.email = false;
    } else {
      dialog({
        title: 'Erro!',
        description: 'Não foi possível enviar o e-mail.',
        iconLeft: 'status-left',
        titleClass: 'danger',
      });
    }
  });
};

const submit = () => {
  emit('finish');
};
</script>

<style scoped>
h3 {
  font-family: Muli, sans-serif !important;
  font-weight: bold !important;
  font-size: 16px !important;
}
dt {
  font-family: Hind, sans-serif;
  font-size: 10px;
  color: var(--gray);
  text-transform: uppercase;
}
dd {
  font-family: Hind, sans-serif;
  font-weight: bold;
  font-size: 16px;
  color: rgba(0, 0, 0, 0.8);
}
</style>
