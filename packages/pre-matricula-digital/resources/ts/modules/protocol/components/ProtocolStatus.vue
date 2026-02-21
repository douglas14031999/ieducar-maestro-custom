<template>
  <div class="container mt-4 pt-lg-4">
    <div class="row mt-3">
      <div class="col-12 col-md-10 offset-md-1">
        <h2 class="font-muli-20-primary mb-4 text-center">
          Resultado de consulta
        </h2>
        <p class="text-center">
          Certifique-se que as iniciais do nome e a data de nascimento são
          compatíveis com as do(a) aluno(a) sendo pesquisado. Se os dados
          estiverem incorretos verifique se o número do protocolo está correto e
          tente novamente.
        </p>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-12 col-md-10 offset-md-1">
        <protocol-consult title="Consulta de inscrição" />
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-12 col-md-10 offset-md-1">
        <div class="m-auto pt-4">
          <div class="protocol card p-3 m-auto">
            <div v-if="preregistration">
              <div v-if="preregistration.status === 'ACCEPTED'">
                <div class="alert badge-green text-center text-uppercase">
                  Deferido
                </div>
              </div>
              <div v-if="preregistration.status === 'WAITING'">
                <div class="alert badge-yellow text-center text-uppercase">
                  Em espera
                </div>
              </div>
              <div v-if="preregistration.status === 'REJECTED'">
                <div class="alert badge-red text-center text-uppercase">
                  Indeferido
                </div>
              </div>
              <div v-if="preregistration.status === 'SUMMONED'">
                <div class="alert badge-purple text-center text-uppercase">
                  Responsáveis Convocados
                </div>
              </div>
              <div v-if="preregistration.status === 'IN_CONFIRMATION'">
                <div
                  class="alert badge-light-orange text-center text-uppercase"
                >
                  Em confirmação
                </div>
              </div>
              <dl class="mb-0">
                <dt>Aluno(a)</dt>
                <dd>
                  {{ preregistration.student.initials }}
                  ({{
                    $filters.formatDate(preregistration.student.dateOfBirth)
                  }})
                </dd>
                <pre-registration-position
                  v-if="preregistration.process.showPriorityProtocol"
                  :preregistration="preregistration"
                  data-test="preregistration-position"
                />
                <dt>Escola</dt>
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
                <dt>Tipo</dt>
                <dd>
                  <span
                    v-if="preregistration.type === 'WAITING_LIST'"
                    class="badge badge-yellow"
                  >
                    Lista de espera
                  </span>
                  <span v-else class="badge badge-blue"> Pré-matrícula </span>
                </dd>
              </dl>
              <div v-if="preregistration.waiting" class="mt-4">
                <div class="alert badge-gray">
                  <p>Este aluno também está em lista de espera:</p>
                  <dl class="mb-0">
                    <dt>Protocolo</dt>
                    <dd>
                      <router-link
                        :to="{
                          name: 'protocol.status',
                          params: { id: preregistration.waiting.protocol },
                        }"
                      >
                        {{ preregistration.waiting.protocol }}
                      </router-link>
                    </dd>
                    <pre-registration-position
                      v-if="
                        preregistration &&
                        preregistration.waiting.process &&
                        preregistration.waiting.process.showPriorityProtocol
                      "
                      :preregistration="preregistration.waiting"
                      data-test="preregistration-position-waiting"
                    />
                    <dt>Escola</dt>
                    <dd>
                      <p class="mb-0">
                        {{ preregistration.waiting.school.name }}
                      </p>
                      <p
                        v-if="preregistration.waiting.school.phone"
                        class="mb-0"
                      >
                        Telefone:
                        {{
                          `(${preregistration.waiting.school.area_code})
${preregistration.waiting.school.phone}`
                        }}
                      </p>
                    </dd>
                  </dl>
                </div>
              </div>
              <div v-if="preregistration.parent" class="mt-4">
                <div class="alert badge-gray">
                  <p>Este(a) aluno(a) também possui uma pré-matrícula:</p>
                  <dl class="mb-0">
                    <dt>Protocolo</dt>
                    <dd>
                      <router-link
                        :to="{
                          name: 'protocol.status',
                          params: { id: preregistration.parent.protocol },
                        }"
                      >
                        {{ preregistration.parent.protocol }}
                      </router-link>
                    </dd>
                    <pre-registration-position
                      v-if="
                        preregistration &&
                        preregistration.parent &&
                        preregistration.parent.process &&
                        preregistration.parent.process.showPriorityProtocol
                      "
                      :preregistration="preregistration.parent"
                      data-test="preregistration-position-parent"
                    />
                    <dt>Escola</dt>
                    <dd>
                      <p class="mb-0">
                        {{ preregistration.parent.school.name }}
                      </p>
                      <p
                        v-if="preregistration.parent.school.phone"
                        class="mb-0"
                      >
                        Telefone:
                        {{
                          `(${preregistration.parent.school.area_code})
${preregistration.parent.school.phone}`
                        }}
                      </p>
                    </dd>
                  </dl>
                </div>
              </div>
              <template v-if="preregistration.status === 'IN_CONFIRMATION'">
                <x-btn
                  outline
                  data-test="btn-more-info"
                  color="primary"
                  class="w-100 mt-4"
                  label="Mais informações"
                  no-caps
                  no-wrap
                  @click="showModal = true"
                />
                <x-btn
                  v-if="isAuthenticated"
                  data-test="btn-continue-on-the-list"
                  color="primary"
                  class="w-100 mt-2"
                  label="Continuar na lista"
                  no-caps
                  no-wrap
                  @click="showModalConfirmKeepInWaitingList = true"
                />
              </template>
              <x-btn
                v-else
                data-test="btn-more-info"
                color="primary"
                class="w-100 mt-4"
                label="Mais informações"
                no-caps
                no-wrap
                @click="showModal = true"
              />
              <protocol-status-modal
                v-model="showModal"
                :preregistration="preregistration"
                data-test="protocol-status-modal"
              />
            </div>
            <div v-else-if="notFound" data-test="not-found-text">
              Nenhum protocolo foi encontrado.
            </div>
            <div
              v-else
              class="d-flex pt-4 pb-4 justify-content-center"
              data-test="loader-content"
            >
              <div class="spinner-border text-primary"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <protocol-confirm-keep-in-waiting-list
      v-if="showModalConfirmKeepInWaitingList && isAuthenticated"
      v-model="showModalConfirmKeepInWaitingList"
      :preregistration="(preregistration as ProtocolStatusPreRegistration)"
      @get-protocol="getProtocol(routeId)"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, inject, ref, watch } from 'vue';
import { Filters } from '@/filters';
import { Nullable } from '@/types';
import PreRegistrationPosition from '@/components/elements/PreRegistrationPosition.vue';
import { Protocol } from '@/modules/protocol/api';
import ProtocolConfirmKeepInWaitingList from '@/modules/protocol/components/ProtocolConfirmKeepInWaitingList.vue';
import ProtocolConsult from '@/components/elements/ProtocolConsult.vue';
import ProtocolStatusModal from './ProtocolStatusModal.vue';
import { ProtocolStatusPreRegistration } from '@/modules/protocol/types';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import { useGeneralStore } from '@/store/general';
import { useLoader } from '@/composables';
import { useRoute } from 'vue-router';

const store = useGeneralStore();

const isAuthenticated = computed(() => store.isAuthenticated);

const { loader: loaderProtocol, data: preregistration } =
  useLoader<Nullable<ProtocolStatusPreRegistration>>();

const route = useRoute();

const $filters = inject('$filters') as Filters;

const showModal = ref(false);
const notFound = ref(false);
const showModalConfirmKeepInWaitingList = ref(false);

const routeId = computed(() => route.params.id as string);

const getProtocol = (protocol: string) => {
  preregistration.value = null;
  notFound.value = false;

  loaderProtocol(() =>
    Protocol.show({
      search: protocol,
    })
  ).then((res) => {
    preregistration.value = res;
    if (res === null) {
      notFound.value = true;
    }
  });
};

const ctx = {
  getProtocol,
};

watch(routeId, (id) => {
  ctx.getProtocol(id);
});

ctx.getProtocol(routeId.value);
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
