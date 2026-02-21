<template>
  <modal
    v-model="open"
    title="Informações da Consulta"
    title-class="text-primary"
    no-footer
  >
    <template #body>
      <div v-if="preregistration">
        <dl class="mb-0">
          <dt>Aluno(a)</dt>
          <dd>
            {{ preregistration.student.initials }}
            ({{ $filters.formatDate(preregistration.student.dateOfBirth) }})
          </dd>
          <pre-registration-position
            v-if="preregistration.process.showPriorityProtocol"
            :preregistration="preregistration"
          />
          <dt class="mt-3">Escola</dt>
          <dd>
            {{ preregistration.school.name }}
          </dd>
        </dl>
        <div
          v-if="
            preregistration.status === 'ACCEPTED' && preregistration.classroom
          "
          class="row"
        >
          <dl class="col">
            <dt class="mt-3">Turma</dt>
            <dd>
              {{ preregistration.classroom.name }}
            </dd>
          </dl>
          <dl class="col">
            <dt class="mt-3">Turno</dt>
            <dd>
              {{ preregistration.classroom.period.name }}
            </dd>
          </dl>
          <dl class="col">
            <dt class="mt-3">Série</dt>
            <dd>
              {{ preregistration.classroom.grade.name }}
            </dd>
          </dl>
        </div>
        <dl
          v-if="
            preregistration.status === 'REJECTED' && preregistration.observation
          "
        >
          <dt class="mt-3">Justificativa do indeferimento</dt>
          <dd>
            {{ preregistration.observation }}
          </dd>
        </dl>
        <dl
          v-if="
            preregistration.status === 'SUMMONED' && preregistration.observation
          "
        >
          <dt class="mt-3">observações da convocação</dt>
          <dd>
            {{ preregistration.observation }}
          </dd>
        </dl>
        <div v-if="preregistration.waiting">
          <div class="alert badge-yellow">
            <p>Este aluno também está em lista de espera:</p>
            <dl class="mb-0">
              <dt>Protocolo</dt>
              <dd>{{ preregistration.waiting.protocol }}</dd>
              <dt>Escola</dt>
              <dd>{{ preregistration.waiting.school.name }}</dd>
            </dl>
          </div>
        </div>
        <div v-if="preregistration.parent">
          <div class="alert badge-yellow">
            <p>Esta é a escolha principal deste(a) aluno(a):</p>
            <dl class="mb-0">
              <dt>Protocolo</dt>
              <dd>{{ preregistration.parent.protocol }}</dd>
              <dt>Escola</dt>
              <dd>{{ preregistration.parent.school.name }}</dd>
            </dl>
          </div>
        </div>
        <dl v-if="preregistration.stage.observation" class="mb-0">
          <dt>Observações e documentos a serem entregues na matrícula</dt>
          <dd v-html="preregistration.stage.observation"></dd>
        </dl>
      </div>
    </template>
  </modal>
</template>

<script setup lang="ts">
import { Filters } from '@/filters';
import Modal from '@/components/elements/Modal.vue';
import PreRegistrationPosition from '@/components/elements/PreRegistrationPosition.vue';
import { ProtocolStatusPreRegistration } from '@/modules/protocol/types';
import { inject } from 'vue';
import { useVModel } from '@vueuse/core';

const props = defineProps<{
  preregistration: ProtocolStatusPreRegistration;
  modelValue: boolean;
}>();

const $filters = inject('$filters') as Filters;

const open = useVModel(props, 'modelValue');
</script>
