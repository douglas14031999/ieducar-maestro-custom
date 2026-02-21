<template>
  <div class="mt-5">
    <skeleton-form-preregistration v-if="step === 'LOADING'" />
    <x-form
      v-if="step !== 'LOADING' && step !== 'PROTOCOL'"
      ref="form"
      :key="forceRenderer + '-force-renderer'"
      :disable-proceed="
        fetchingPrimaryAddressLatLng || fetchingSecondaryAddressLatLng
      "
      :schema="schema"
      :loading="loadingSubmit || loadingMatch"
      :initial-values="initialValues"
      @submit="next"
    >
      <template #default="{ errors, setFieldValue }">
        <div class="col-12">
          <match
            v-if="step === 'MATCH'"
            v-model:student="student"
            v-model:fields="fields"
            :process="stagePayload.stage.process"
            :stage="stagePayload.stage"
            :errors="errors"
            class="container-pmd"
          />
          <responsible-fields
            v-if="step === 'RESPONSIBLE'"
            v-model:fields="fields.responsible"
            v-model:responsible="responsible"
            v-model:fetchingPrimaryAddressLatLng="fetchingPrimaryAddressLatLng"
            v-model:fetchingSecondaryAddressLatLng="
              fetchingSecondaryAddressLatLng
            "
            :errors="errors"
            :set-field-value="setFieldValue"
            class="container-pmd"
            @clean="handleClearData('RESPONSIBLE')"
          />
          <student-fields
            v-if="step === 'STUDENT'"
            v-model:stage="stagePayload.stage"
            v-model:fields="fields.student"
            v-model:student="student"
            v-model:responsible="responsible"
            :errors="errors"
            class="container-pmd"
            @clean="handleClearData('STUDENT')"
            @change-primary-address="handleChangePrimaryAddress"
          />
          <review
            v-if="step === 'REVIEW'"
            :process="stagePayload.stage.process"
            :fields="fields"
            :responsible="responsible"
            :student="student"
            :errors="errors"
            @edit-responsible="step = 'RESPONSIBLE'"
            @edit-student="step = 'STUDENT'"
          />
          <div
            v-if="Object.keys(errors).length > 0"
            class="text-danger col-6 offset-3 text-center mt-5 alert alert-danger"
          >
            Preencha todos os campos corretamente
          </div>
        </div>
      </template>
    </x-form>

    <modal
      v-model="open"
      icon-left="status-green"
      title="Aluno(a) Encontrado(a)"
      no-footer
    >
      <template #body>
        Confirme se as iniciais do nome e a data de nascimento do(a) aluno(a)
        estão corretas:
        <div v-if="student.match" class="mt-4">
          <div class="row">
            <dl class="col">
              <dt class="font-hind font-size-10">Iniciais</dt>
              <dd>
                {{ student.match.initials }}
              </dd>
            </dl>
            <dl class="col">
              <dt class="font-hind font-size-10">Data de nascimento</dt>
              <dd>
                {{ $filters.formatDate(student.match.dateOfBirth as string) }}
              </dd>
            </dl>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <x-btn
              label="Não são estas"
              color="gray-500"
              outline
              class="w-100"
              no-caps
              no-wrap
              @click="open = false"
            />
          </div>
          <div class="col-6">
            <x-btn
              label="Estão corretas"
              color="primary"
              class="border-primary w-100"
              no-caps
              no-wrap
              @click="doMatch"
            />
          </div>
        </div>
      </template>
    </modal>

    <protocol
      v-if="stagePayload.stage && step === 'PROTOCOL'"
      v-model:process="stagePayload.stage.process"
      :preregistrations="preregistrations"
      :responsible="responsible"
      :student="student"
      @finish="finish"
    />
  </div>
</template>

<script setup lang="ts">
import {
  AppContext,
  computed,
  getCurrentInstance,
  inject,
  onMounted,
  ref,
  watch,
} from 'vue';
import {
  Fields,
  Match as MatchType,
  PreRegistrationOverview,
  PreRegistrationResponsibleField,
  PreRegistrationStage,
  PreRegistrationStudentField,
  PreregistrationSubmit,
  PreregistrationSubmitInput,
} from '@/modules/preregistration/types';
import {
  newAddress,
  parseResponsibleFieldsFromProcess,
  parseStudentFieldsFromProcess,
} from '@/util';
import {
  useLoader,
  useLoaderAndShowErrorByModal,
  useModal,
} from '@/composables';
import { useRoute, useRouter } from 'vue-router';
import { Filters } from '@/filters';
import { FormSchema } from '@/types';
import Match from '@/modules/preregistration/components/Match.vue';
import Modal from '@/components/elements/Modal.vue';
import { Preregistration } from '@/modules/processes/types';
import { Preregistration as PreregistrationApi } from '@/modules/preregistration/api';
import Protocol from './Protocol.vue';
import ResponsibleFields from './ResponsibleFields.vue';
import Review from './Review.vue';
import SkeletonFormPreregistration from '@/components/loaders/components/FormPreregistration.vue';
import StudentFields from './StudentFields.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XForm from '@/components/x-form/XForm.vue';
import { analytics } from '@/packages';
import { useGeneralStore } from '@/store/general';

const { dialog } = useModal();

const { loader, data: stagePayload } = useLoader<{
  stage: PreRegistrationStage;
  preregistration?: Preregistration;
}>({
  stage: {} as PreRegistrationStage,
  preregistration: undefined,
});

const {
  data: matchesFounded,
  loader: loaderSubmitMatch,
  loading: loadingMatch,
} = useLoader<MatchType[]>();

const appContext = getCurrentInstance()?.appContext;

const { loader: loaderSubmit, loading: loadingSubmit } =
  useLoaderAndShowErrorByModal<PreregistrationSubmit>(appContext as AppContext);

const { page: pageview } = analytics();
const store = useGeneralStore();
const route = useRoute();
const router = useRouter();
const form = ref();

const preregistrations = ref<PreRegistrationOverview[]>([]);
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
const newResponsible = ref<PreRegistrationResponsibleField>(
  {} as PreRegistrationResponsibleField
);
const newStudent = ref<PreRegistrationStudentField>(
  {} as PreRegistrationStudentField
);
const step = ref<
  'LOADING' | 'REVIEW' | 'MATCH' | 'RESPONSIBLE' | 'STUDENT' | 'PROTOCOL'
>('LOADING');
const initialValues = ref({});
const forceRenderer = ref(0);
const fetchingPrimaryAddressLatLng = ref(false);
const fetchingSecondaryAddressLatLng = ref(false);

const schema = computed<FormSchema>(() => {
  return {
    fields: [],
    buttons: [
      {
        type: 'button',
        label: 'Voltar',
        class: 'btn btn-block btn-outline-light',
        containerClass: 'offset-md-3 col-md-3',
        block: true,
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
  } as FormSchema;
});

const $filters = inject('$filters') as Filters;

const open = ref(false);

watch(step, (value) => {
  if (value === 'MATCH') {
    pageview({
      path: '/inscricao/dados-do-aluno',
    });
  } else if (value === 'RESPONSIBLE') {
    pageview({
      path: '/inscricao/dados-do-responsavel',
    });
  } else if (value === 'STUDENT') {
    pageview({
      path: '/inscricao/dados-do-aluno',
    });
  } else if (value === 'REVIEW') {
    pageview({
      path: '/inscricao/conferencia-de-dados',
    });
  } else if (value === 'PROTOCOL') {
    pageview({
      path: '/inscricao/protocolo',
    });
  }
});

const getData = () => {
  loader(() =>
    PreregistrationApi.list({
      id: route.params.id as string,
      code: route.query?.code as string,
      load: !!route.query?.code,
    })
  ).then((response) => {
    if (response.stage.status !== 'OPEN') {
      router.push('/');
      return;
    }

    const { fields: responsibleFields, data: responsibleData } =
      parseResponsibleFieldsFromProcess(response.stage.process);

    const { fields: studentFields, data: studentData } =
      parseStudentFieldsFromProcess(response.stage.process);

    fields.value.responsible = responsibleFields.sortBy('order');
    responsible.value = responsibleData;

    fields.value.student = studentFields.sortBy('order');
    student.value = studentData;

    if (!store.isProduction) {
      const localResponsible = window.localStorage.getItem('responsible');
      const localStudent = window.localStorage.getItem('student');

      if (localResponsible) {
        responsible.value = {
          ...responsibleData,
          ...JSON.parse(localResponsible),
        };
      }

      if (localStudent) {
        student.value = {
          ...studentData,
          ...JSON.parse(localStudent),
          useSecondSchool: false,
        };
      }
    }

    if (response.preregistration) {
      student.value = {
        ...studentData,
        ...response.preregistration.student,
      };

      responsible.value = {
        ...responsibleData,
        ...response.preregistration.responsible,
      };
    }

    newResponsible.value = { ...responsibleData };
    newStudent.value = { ...studentData };

    step.value = 'MATCH';
  });
};

const finish = () => {
  step.value = 'MATCH';
  initialValues.value = responsible.value;
  student.value = { ...newStudent.value };
};

const prev = () => {
  if (step.value === 'MATCH') {
    router.back();
  } else if (step.value === 'RESPONSIBLE') {
    router.back();
  } else if (step.value === 'STUDENT') {
    step.value = 'RESPONSIBLE';
  } else if (step.value === 'REVIEW') {
    step.value = 'STUDENT';
  }

  window.scrollTo(0, 0);
};

const next = async (
  model: {
    address: {
      lat: number;
      lng: number;
    };
  } & PreRegistrationResponsibleField
) => {
  if (step.value === 'MATCH') {
    await ctx.submitMatch();

    if (student.value.responsible_name) {
      responsible.value.responsible_name = student.value.responsible_name;
    }
    if (student.value.responsible_cpf) {
      responsible.value.responsible_cpf = student.value.responsible_cpf;
    }
    if (student.value.responsible_date_of_birth) {
      responsible.value.responsible_date_of_birth =
        student.value.responsible_date_of_birth;
    }
    if (student.value.responsible_phone) {
      responsible.value.responsible_phone = student.value.responsible_phone;
    }
  } else if (step.value === 'RESPONSIBLE') {
    if (
      (!model.address.lat && !model.address.lng) ||
      (model.address.lat === 0 && model.address.lng === 0)
    ) {
      return;
    }

    responsible.value = { ...responsible.value, ...model };
    ctx.saveOnStorage();
    step.value = 'STUDENT';
  } else if (step.value === 'STUDENT') {
    student.value = { ...student.value, ...model };
    ctx.saveOnStorage();

    let canNext = ctx.submitToReview();

    if (!canNext) {
      return;
    }
  } else if (step.value === 'REVIEW') {
    await ctx.submit();
  }

  window.scrollTo(0, 0);
};

const submitMatch = async () => {
  await loaderSubmitMatch(() =>
    PreregistrationApi.postMatch({
      stage: stagePayload.value.stage,
      student: student.value,
    })
  );

  const match = matchesFounded.value.length ? matchesFounded.value[0] : null;
  const restriction = stagePayload.value.stage.restrictionType;

  // Retorna um match por já existir outra inscrição para o ano

  if (
    stagePayload.value.stage.process.onePerYear &&
    match &&
    match.type === 'PRE_REGISTRATION'
  ) {
    dialog({
      title: 'Aluno(a) possui inscrição para o ano',
      titleClass: 'danger',
      description: `Como o aluno já possui uma inscrição em aberto para o ano não será possível seguir nesta opção. Clique em \'Voltar\' e efetue a inscrição em outro tipo de período.`,
    });

    return;
  }

  // Não há retrição, avança

  if (restriction === 'NONE') {
    step.value = 'RESPONSIBLE';

    return;
  }

  // Aluno deveria ter matrícula no último ano e não possui

  if (restriction === 'REGISTRATION_LAST_YEAR' && match === null) {
    dialog({
      title: 'Aluno(a) não possui matrícula no ano passado',
      titleClass: 'danger',
      description: `Como o aluno não possui uma matrícula no ano passado não será possível seguir nesta opção. Clique em \'Voltar\' e efetue a inscrição em outro tipo de período.`,
    });

    return;
  }

  // Aluno deveria ter matrícula no ano atual e não possui

  if (restriction === 'REGISTRATION_CURRENT_YEAR' && match === null) {
    dialog({
      title: 'Aluno(a) não possui matrícula no ano atual',
      titleClass: 'danger',
      description: `Como o aluno não possui uma matrícula no ano atual não será possível seguir nesta opção. Clique em \'Voltar\' e efetue a inscrição em outro tipo de período.`,
    });

    return;
  }

  // Aluno não deveria ter matrícula no ano atual e possui

  if (
    restriction === 'NO_REGISTRATION_CURRENT_YEAR' &&
    match &&
    match.type === 'NO_REGISTRATION_CURRENT_YEAR'
  ) {
    dialog({
      title: 'Aluno(a) já possui matrícula no ano atual',
      titleClass: 'danger',
      description: `Como o aluno já possui uma matrícula no ano atual não será possível seguir nesta opção. Clique em \'Voltar\' e efetue a inscrição em outro tipo de período.`,
    });

    return;
  }

  // Aluno não deveria ter matrícula no turno e ano do processo

  if (
    restriction === 'NO_REGISTRATION_PERIOD_CURRENT_YEAR' &&
    match &&
    match.type === 'NO_REGISTRATION_PERIOD_CURRENT_YEAR'
  ) {
    dialog({
      title: 'Aluno(a) já possui matrícula no turno e ano',
      titleClass: 'danger',
      description: `Como o aluno já possui uma matrícula no turno e ano do processo não será possível seguir nesta opção. Clique em \'Voltar\' e efetue a inscrição em outro tipo de período.`,
    });

    return;
  }

  // Aluno não deveria ter cadastro no município

  if (restriction === 'NEW_STUDENT' && match && match.type === 'NEW_STUDENT') {
    dialog({
      title: 'Aluno(a) já possui cadastro na rede',
      titleClass: 'danger',
      description: `Como o aluno já está cadastrado na rede não será possível seguir nesta opção. Clique em \'Voltar\' e efetue a inscrição em outro tipo de período.`,
    });

    return;
  }

  if (match) {
    student.value.match = match;
    open.value = true;
  } else {
    step.value = 'RESPONSIBLE';
  }
};

const doMatch = () => {
  step.value = 'RESPONSIBLE';
  open.value = false;
};

const submit = async () => {
  const { grade, period, school } = student.value;

  const mappedResponsible = Object.keys(responsible.value)
    .filter(
      (f) =>
        f.substring(0, 6) === 'field_' || f.substring(0, 12) === 'responsible_'
    )
    .map((key) => {
      return {
        field: key,
        value: responsible.value[key as keyof typeof responsible.value]
          ? String(responsible.value[key as keyof typeof responsible.value])
          : null,
      };
    });

  const mappedStudent = Object.keys(student.value)
    .filter(
      (f) => f.substring(0, 6) === 'field_' || f.substring(0, 8) === 'student_'
    )
    .map((key) => ({
      field: key,
      value: student.value[key as keyof typeof student.value]
        ? String(student.value[key as keyof typeof student.value])
        : null,
    }));

  const optionalSchool =
    student.value.useSecondSchool ||
    stagePayload.value.stage?.type === 'WAITING_LIST'
      ? student.value.secondSchool
      : null;

  const optionalPeriod =
    student.value.useSecondSchool ||
    stagePayload.value.stage?.type === 'WAITING_LIST'
      ? student.value.secondPeriod
      : null;

  if (!responsible.value.address.manualChangeLocation) {
    responsible.value.address.manualChangeLocation = false;
  }

  if (
    responsible.value.useSecondAddress &&
    !responsible.value.secondAddress.manualChangeLocation
  ) {
    responsible.value.secondAddress.manualChangeLocation = false;
  }

  const input: PreregistrationSubmitInput = {
    process: stagePayload.value?.stage.process.id,
    stage: stagePayload.value?.stage.id,
    type: stagePayload.value?.stage.type,
    grade,
    period,
    school,
    optionalSchool,
    optionalPeriod: optionalPeriod || period,
    address: responsible.value.address,
    optionalAddress: responsible.value.useSecondAddress
      ? responsible.value.secondAddress
      : null,
    relationType: responsible.value.relationType,
    responsible: mappedResponsible,
    student: mappedStudent,
    externalPerson: student.value.match ? student.value.match.id : null,
    waitingList: student.value.waitingList,
  };

  loaderSubmit(() => PreregistrationApi.post(input)).then((res) => {
    preregistrations.value = res.preregistrations;
    step.value = 'PROTOCOL';
  });
};

const submitToReview = () => {
  // Validação manual para checar se o CPF do responsável e do aluno são iguais
  if (
    responsible.value.responsible_cpf &&
    student.value.student_cpf &&
    responsible.value.responsible_cpf === student.value.student_cpf &&
    responsible.value.relationType !== 'SELF'
  ) {
    form.value.$refs.form.setErrors({
      student_cpf: 'CPF igual ao do responsável',
    });

    return false;
  }

  // Validação manual para checar se ao menos uma escola foi informada
  if (!student.value.school && !student.value.secondSchool) {
    form.value.$refs.form.setErrors({
      atLeastOneSchool: 'Uma escola precisa ser selecionada',
    });

    return false;
  }

  step.value = 'REVIEW';

  return true;
};

const saveOnStorage = () => {
  if (!store.isProduction) {
    setInterval(() => {
      window.localStorage.setItem(
        'responsible',
        JSON.stringify(responsible.value)
      );
      window.localStorage.setItem('student', JSON.stringify(student.value));
    }, 5000);
  }
};

const handleChangePrimaryAddress = ({
  changed,
  secondAddress,
}: {
  changed: boolean;
  secondAddress: boolean;
}) => {
  responsible.value[
    secondAddress ? 'secondAddress' : 'address'
  ].manualChangeLocation = changed;
};

const handleClearData = (type: 'RESPONSIBLE' | 'STUDENT') => {
  if (type === 'RESPONSIBLE') {
    responsible.value = {
      ...newResponsible.value,
      address: newAddress(),
    };

    if (responsible.value.useSecondAddress) {
      responsible.value.secondAddress = newAddress();
      responsible.value.useSecondAddress = false;
    }

    initialValues.value = responsible.value;
  } else if (type === 'STUDENT') {
    student.value = { ...newStudent.value };
    initialValues.value = student.value;
  }
  forceRenderer.value += 1;
};

onMounted(() => getData());

/**
 * According to this issue:
 * https://stackoverflow.com/questions/72084274/vue3-using-vitest-tohavebeencalled-method
 *
 * This is a workaround to make the method mock work on tests environments.
 */
const ctx = {
  submitMatch,
  saveOnStorage,
  submitToReview,
  submit,
};

ctx.saveOnStorage();
</script>
