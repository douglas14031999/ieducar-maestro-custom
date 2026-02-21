<template>
  <div>
    <x-form
      :schema="schema"
      :loading="loading"
      :initial-values="studentModel"
      @submit="next"
    >
      <template #default="slot">
        <div class="col-12">
          <div class="row">
            <div class="col-md-8 mt-5 offset-md-2">
              <h2 class="font-muli-20-primary mb-4">
                Identifique o(a) aluno(a) para encontrar o(s) protocolo(s):
              </h2>
              <p class="text-justify font-hind">
                Preencha os dados abaixo para identificar se o(a)
                <strong>aluno(a)</strong> já possui alguma inscrição de
                pré-matrícula.
              </p>
              <div class="d-flex justify-content-center mt-5 row">
                <slot v-bind="slot" name="default"></slot>
              </div>
              <div
                v-if="!!slot.errors && Object.keys(slot.errors).length > 0"
                class="row mt-5"
              >
                <div
                  class="text-danger col-6 offset-3 text-center mb-5 alert alert-danger"
                >
                  Preencha todos os campos corretamente
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </x-form>

    <modal
      v-model="open"
      no-footer
      title="Resultado da consulta"
      title-class="text-primary"
      :initial-loading="
        student.preregistrations && student.preregistrations.length === 0
      "
    >
      <template #body>
        <div>
          O(s) protocolo(s) abaixo foram encontrados para este aluno(a).
          Certifique-se que as letras iniciais e a data de nascimento são
          compatíveis com as do(a) aluno(a) sendo pesquisado.
        </div>
        <div
          v-if="student.preregistrations && student.preregistrations.length"
          class="mt-4"
        >
          <div
            v-for="preregistration in student.preregistrations"
            :key="preregistration.id"
          >
            <div class="row">
              <dl class="col mb-0">
                <dt class="font-hind font-size-10">Protocolo</dt>
                <dd class="font-hind-18-primary">
                  <router-link
                    :to="{
                      name: 'protocol.status',
                      params: { id: preregistration.protocol },
                    }"
                  >
                    #{{ preregistration.protocol }}
                  </router-link>
                </dd>
              </dl>
              <dl class="col-8">
                <dt class="font-hind font-size-10">Situação</dt>
                <dd>
                  <span
                    class="badge"
                    :class="stageStatusBadge(preregistration.status)"
                  >
                    {{ preRegistrationStatusText(preregistration.status) }}
                  </span>
                </dd>
              </dl>
            </div>
            <div class="row">
              <dl class="col">
                <dt class="font-hind font-size-10">Ano</dt>
                <dd>
                  {{ preregistration.process.schoolYear.year }}
                </dd>
              </dl>
              <dl class="col">
                <dt class="font-hind font-size-10">Iniciais do Nome</dt>
                <dd>
                  {{ preregistration.student.initials }}
                </dd>
              </dl>
              <dl class="col">
                <dt class="font-hind font-size-10">Data de nascimento</dt>
                <dd>
                  {{ $filters.formatDate(preregistration.student.dateOfBirth) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </template>
    </modal>
  </div>
</template>

<script setup lang="ts">
import { FormSchema, Student } from '@/types';
import { inject, ref } from 'vue';
import { preRegistrationStatusText, stageStatusBadge } from '@/util';
import { useLoader, useModal } from '@/composables';
import { Filters } from '@/filters';
import { FindProtocolPreRegistration } from '@/modules/protocol/types';
import Modal from '@/components/elements/Modal.vue';
import { Protocol } from '@/modules/protocol/api';
import XForm from '@/components/x-form/XForm.vue';
import { useRouter } from 'vue-router';
import { useVModel } from '@vueuse/core';

const { dialog } = useModal();

defineEmits<{
  (action: 'update:modelValue', payload: string): void;
}>();

const props = defineProps<{
  student: Student;
  type: 'BIRTH_CERTIFICATE' | 'NAME_DATE_OF_BIRTH' | 'RG' | 'CPF';
}>();

const { loader, loading } = useLoader<FindProtocolPreRegistration[]>();

const $filters = inject('$filters') as Filters;

const router = useRouter();

const open = ref(false);

const studentModel = useVModel(props, 'student');

const close = () => {
  open.value = false;
};

const prev = () => {
  router.push('/onde-encontro-o-protocolo');
};

const next = async () => {
  await ctx.submit();
  window.scrollTo(0, 0);
};

const schema = ref<FormSchema>({
  fields: [],
  buttons: [
    {
      type: 'button',
      label: 'Voltar',
      class: 'btn btn-block btn-outline-light',
      containerClass: 'offset-md-3 col-md-3',
      action: prev,
    },
    {
      type: 'submit',
      label: 'Prosseguir',
      class: 'btn btn-block btn-primary',
      containerClass: 'col-md-3 mt-3 mt-md-0',
      block: true,
    },
  ],
  buttonsContainer: {
    class: 'row mt-5',
  },
});

const submit = async () => {
  loader(() =>
    Protocol.postFindProtocol({
      type: props.type,
      studentModel: studentModel.value,
    })
  ).then((preregistrations) => {
    studentModel.value.preregistrations = preregistrations.sort((a, b) =>
      a.process.schoolYear.year > b.process.schoolYear.year ? -1 : 1
    );

    if (studentModel.value.preregistrations.length === 0) {
      dialog({
        title: 'Nenhum protocolo encontrado',
        titleClass: 'danger',
        iconLeft: 'status-red',
        description: `Verifique se os dados informados estão corretos. Caso não encontre nenhum resultado, clique em 'Voltar' e
          efetue a validação com outro tipo de dado, ou efetue uma pré-matrícula para o(a) aluno(a)`,
      });
    } else {
      open.value = true;
    }
  });
};

const ctx = {
  submit,
};

defineExpose({
  close,
});
</script>
