<template>
  <main class="container" style="max-width: 740px">
    <h2 class="font-muli-20-primary">Crie o processo de pré-matrícula</h2>
    <p>
      Revise os dados do processo a ser criado e prossiga para finalizá-lo. Você
      pode pré-visualizar os formulários criados para esse processo e editá-los
      se necessário.
      <strong>
        Lembre-se que depois de criado, um processo não poderá ser editado.
      </strong>
    </p>
    <skeleton-page-process-check v-if="!process" class="mt-5" />
    <template v-else>
      <div class="d-flex justify-content-between mt-5">
        <h4>{{ process.name }} ({{ process.schoolYear.year }})</h4>
      </div>
      <div class="row">
        <div
          v-for="stage in process.stages"
          :key="stage.id"
          class="col-12 mb-3"
        >
          <x-card bordered hoverable>
            <x-card-section class="pb-0">
              <h5 class="text-h5">
                {{ stageTypeText(stage.type) }}
              </h5>
            </x-card-section>
            <x-card-section class="pt-0">
              <div class="mt-2 d-flex align-items-center text-size-15">
                <i class="fa fa-calendar text-primary mr-2"></i>
                <span class="text-gray-600">
                  {{ $filters.formatDateTime(stage.startAt) }} à
                  {{ $filters.formatDateTime(stage.endAt) }}
                </span>
              </div>
              <div class="mt-3">
                <small
                  v-for="period in process.periods"
                  :key="period.id"
                  :class="stageStatusBadge(stage.status)"
                  class="badge mr-2"
                >
                  {{ period.name }}
                </small>
              </div>
            </x-card-section>
          </x-card>
        </div>
      </div>
      <div class="mt-5 mb-4">
        <h4>Formulários</h4>
      </div>
      <div class="row">
        <div class="col-12">
          <div
            class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4"
          >
            <p>Dados do(a) responsável</p>
            <div>
              <x-btn
                :loading="loading"
                type="submit"
                color="primary"
                outline
                no-caps
                no-wrap
                label="Editar"
                @click="
                  $router.push({
                    name: 'process.fields',
                    params: { id: process.id },
                  })
                "
              />
            </div>
          </div>
          <div class="row">
            <div
              v-for="field in fields.responsible"
              :key="field.id"
              class="col-12 col-md-6 mb-3"
            >
              <label class="toggle-checkbox">
                <input type="checkbox" disabled :value="field.id" />
                <div
                  :class="{
                    'toggle-required': field.required,
                    'toggle-weight': field.weight !== 0,
                  }"
                  class="toggle-text"
                >
                  {{ field.field.name }}
                </div>
              </label>
            </div>
            <div class="col-12 mb-3">
              <label class="toggle-checkbox">
                <input type="checkbox" disabled />
                <div class="toggle-text toggle-required">Endereço</div>
              </label>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <x-btn
                color="primary"
                class="w-100"
                no-caps
                no-wrap
                label="Pré-visualizar formulário"
                size="lg"
                @click="responsibleFieldsOpen = true"
              />
            </div>
            <modal
              v-model="responsibleFieldsOpen"
              title="Dados do(a) Responsável"
              body-class="bg-background"
              no-footer
              :large="true"
            >
              <template #body>
                <responsible-fields
                  v-model:fields="fields.responsible"
                  v-model:responsible="responsible"
                  class="p-2"
                />
              </template>
            </modal>
          </div>
        </div>
        <div class="col-12 mt-5">
          <div
            class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4"
          >
            <p>Dados do(a) aluno(a)</p>
            <div>
              <x-btn
                :loading="loading"
                type="submit"
                color="primary"
                outline
                no-caps
                no-wrap
                label="Editar"
                @click="
                  $router.push({
                    name: 'process.fields',
                    params: { id: process.id },
                  })
                "
              />
            </div>
          </div>
          <div class="row">
            <div
              v-for="field in fields.student"
              :key="field.id"
              class="col-12 col-md-6 mb-3"
            >
              <label class="toggle-checkbox">
                <input type="checkbox" disabled :value="field.id" />
                <div
                  :class="{
                    'toggle-required': field.required,
                    'toggle-weight': field.weight !== 0,
                  }"
                  class="toggle-text"
                >
                  {{ field.field.name }}
                </div>
              </label>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <x-btn
                color="primary"
                class="w-100"
                no-caps
                no-wrap
                label="Pré-visualizar formulário"
                size="lg"
                @click="studentFieldsOpen = true"
              />
            </div>
            <modal
              v-model="studentFieldsOpen"
              no-footer
              title="Dados do(a) aluno(a)"
              body-class="bg-background"
              :large="true"
            >
              <template #body>
                <student-fields
                  v-model:student="student"
                  v-model:responsible="responsible"
                  v-model:stage="previewStage"
                  v-model:fields="fields.student"
                  class="p-2"
                />
              </template>
            </modal>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-end mt-5 mb-3">
        <h4 class="m-0">Critérios</h4>
        <div>
          <x-btn
            :loading="loading"
            type="submit"
            color="primary"
            outline
            no-caps
            no-wrap
            label="Editar"
            @click="
              $router.push({
                name: 'process.fields',
                params: { id: process.id },
              })
            "
          />
        </div>
      </div>
      <hr />
      <div class="row">
        <div v-if="priorityFields?.length === 0" class="col-12 text-muted">
          Nenhum critério de prioridade foi definido.
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div
            v-for="field in priorityFields"
            :key="field.id"
            class="line-custom mb-3"
          >
            <div class="row">
              <div class="col-11 text-left">
                {{ field.field.name }}
              </div>
              <div class="col-1">
                {{ field.weight }}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-5">
        <div class="offset-md-3 col-md-3">
          <x-btn
            data-test="btn-back"
            type="submit"
            outline
            color="gray-500"
            class="w-100 h-100 flex-row justify-content-center"
            size="lg"
            no-caps
            no-wrap
            label="Voltar"
            @click="handleClickBack"
          />
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
          <x-btn
            data-test="btn-submit"
            :loading="loading"
            type="submit"
            color="primary"
            class="w-100 h-100 flex-row justify-content-center"
            size="lg"
            no-caps
            no-wrap
            label="Prosseguir"
            loading-normal
            @click="submit"
          />
        </div>
      </div>
    </template>
  </main>
</template>

<script setup lang="ts">
import {
  Fields,
  PreRegistrationResponsibleField,
  PreRegistrationStage,
  PreRegistrationStudentField,
} from '@/modules/preregistration/types';
import { computed, inject, onMounted, ref } from 'vue';
import {
  parseResponsibleFieldsFromProcess,
  parseStudentFieldsFromProcess,
  stageStatusBadge,
  stageTypeText,
} from '@/util';
import { Filters } from '@/filters';
import Modal from '@/components/elements/Modal.vue';
import { Process as ProcessApi } from '@/modules/processes/api';
import { ProcessCheck } from '@/modules/processes/types';
import ResponsibleFields from '@/modules/preregistration/components/ResponsibleFields.vue';
import SkeletonPageProcessCheck from '@/components/loaders/pages/PageProcessCheck.vue';
import StudentFields from '@/modules/preregistration/components/StudentFields.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import { analytics } from '@/packages';
import { useLoader } from '@/composables';
import { useRouter } from 'vue-router';

const props = withDefaults(
  defineProps<{
    id: string;
    newProcess?: 'true' | 'false';
  }>(),
  {
    newProcess: 'false',
  }
);

const $filters = inject('$filters') as Filters;

const { loader, data: process } = useLoader<ProcessCheck>();

const { page: pageview } = analytics();

const router = useRouter();

const loading = ref(false);
const responsibleFieldsOpen = ref(false);
const studentFieldsOpen = ref(false);
const fields = ref<Fields>({
  responsible: [],
  student: [],
});
const responsible = ref<PreRegistrationResponsibleField>(
  {} as PreRegistrationResponsibleField
);
const student = ref<PreRegistrationStudentField>(
  {} as PreRegistrationStudentField
);

const previewStage = computed<PreRegistrationStage>(
  () =>
    ({
      id: 0,
      renewalAtSameSchool: false,
      radius: null,
      type: 'REGISTRATION',
      process: process.value,
    } as unknown as PreRegistrationStage)
);

const priorityFields = computed(() =>
  process.value?.fields.filter((f) => f.weight !== 0).sortBy('weigth')
);

const submit = () => {
  router.push({
    name: 'processes',
  });
};

const getData = () => {
  loader(() =>
    ProcessApi.show({
      id: props.id,
    })
  ).then(() => {
    const { fields: responsibleFields, data: responsibleData } =
      parseResponsibleFieldsFromProcess(process.value);

    const { fields: studentFields, data: studentData } =
      parseStudentFieldsFromProcess(process.value);

    fields.value.responsible = responsibleFields.sortBy('order');
    responsible.value = responsibleData;

    fields.value.student = studentFields.sortBy('order');
    student.value = studentData;

    if (props.newProcess === 'true') {
      pageview({
        path: '/processos/criar-novo/validacao-das-informacoes',
      });
    } else {
      pageview({
        path: `/processos/${process.value.name
          .toLowerCase()
          .replaceAll(' ', '-')}/periodos`,
      });
    }
  });
};

const handleClickBack = () => {
  router.push({
    name: 'process.periods',
    params: {
      id: props.id,
      newProcess: props.newProcess,
    },
  });
};

onMounted(() => getData());
</script>
