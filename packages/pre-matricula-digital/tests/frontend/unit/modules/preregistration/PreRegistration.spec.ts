/// <reference types="@types/google.maps" />
import '@/plugin/sortby';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { useRoute, useRouter } from 'vue-router';
import PreRegistration from '@/modules/preregistration/components/PreRegistration.vue';
import { PreRegistrationStage } from '@/modules/preregistration/types';
import { Preregistration as PreregistrationApi } from '@/modules/preregistration/api';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import filters from '@/filters';
import { initialize } from '../../mocks/google';
import { expect, vi } from 'vitest';
import { analytics } from '@/packages';
import { AnalyticsInstance } from 'analytics';
import { useModal } from '@/composables';
import {Preregistration} from "@/modules/processes/types";

const dialogSpy = vi.fn();

vi.mock('@/composables/useModal');
vi.mock('@/modules/preregistration/api');

const spyPush = vi.fn();
const spyBack = vi.fn();

vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
    back: spyBack,
  }),
  useRoute: () => ({
    params: {
      id: '1',
    },
  }),
}));

vi.mock('@/packages');

const stageMock: PreRegistrationStage = {
  allowWaitingList: true,
  id: '1',
  process: {
    forceSuggestedGrade: true,
    gradeAgeRangeLink: null,
    id: '1',
    messageFooter:
      'Acompanhe a publicação do resultado classificatório e convocação no site da Prefeitura.',
    name: 'PROCESSO DE TESTES 2022',
    showPriorityProtocol: true,
    grades: [
      {
        endBirth: '2016-03-31',
        id: '22',
        name: '1º Ano',
        startBirth: '2015-03-31',
      },
    ],
    periods: [
      {
        id: '1',
        name: 'Matutino',
      },
    ],
    schoolYear: { year: 2022 },
    schools: [
      {
        id: '12345',
        latitude: 0,
        longitude: 0,
        name: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
      },
    ],
    vacancies: [
      {
        available: 8,
        grade: 6,
        period: 1,
        school: 12345,
        total: 25,
      },
    ],
    fields: [
      {
        field: {
          group: 'RESPONSIBLE',
          id: '1',
          internal: 'responsible_name',
          name: 'Nome do(a) responsável',
          options: [],
          type: 'TEXT',
          description: null,
        },
        id: '262',
        order: 1,
        required: true,
        weight: 0,
      },
      {
        field: {
          group: 'STUDENT',
          id: '9',
          internal: 'student_name',
          name: 'Nome',
          options: [],
          type: 'TEXT',
          description: null,
        },
        id: '267',
        order: 2,
        required: true,
        weight: 0,
      },
    ],
    allowResponsibleSelectMapAddress: false,
    blockIncompatibleAgeGroup: false,
    onePerYear: false,
    waitingListLimit: 9,
    minimumAge: null,
  },
  radius: 1000,
  renewalAtSameSchool: false,
  status: 'OPEN',
  type: 'REGISTRATION',
  restrictionType: 'REGISTRATION_LAST_YEAR',
};

const onMounted = async (
  stage = stageMock,
  error?: boolean
) => {
  if (!error) {
    vi.mocked(PreregistrationApi.list).mockResolvedValueOnce({
      stage: stage,
      preregistration: undefined,
    });
  } else {
    vi.mocked(PreregistrationApi.list).mockRejectedValueOnce({
      stage: stage,
      preregistration: undefined,
    });
  }
  vi.mocked(analytics).mockImplementation(
    () =>
      ({
        page: vi.fn(),
      } as unknown as AnalyticsInstance)
  );

  vi.mocked(useModal).mockImplementation(() => ({
    dialog: dialogSpy,
  }));

  const wrapper = mount(PreRegistration, {
    global: {
      plugins: [createTestingPinia()],
      stubs: [
        'x-field',
        'google-maps',
        'google-maps-marker-component',
        'google-maps-markers',
      ],
      directives: {
        tooltip: VTooltip,
      },
      provide: {
        $filters: filters,
      },
      mocks: {
        google: vi.fn(() => google),
      },
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('PreRegistration', () => {
  let wrapper: VueWrapper<InstanceType<typeof PreRegistration>>;

  beforeEach(async () => {
    initialize();

    wrapper = await onMounted();

    await wrapper.vm.$nextTick();
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  test('the component exists', () => {
    expect(wrapper).toBeTruthy();
  });

  test('should match snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  test('the initial variables must be loaded with the correct data', () => {
    expect(wrapper.vm.stagePayload.stage).toMatchObject(stageMock);
    expect(wrapper.vm.fields.responsible.length).toBe(1);
    expect(wrapper.vm.fields.student.length).toBe(1);
    expect(wrapper.vm.responsible).toMatchObject({
      relationType: null,
      address: {
        postalCode: '',
        address: '',
        number: '',
        complement: '',
        neighborhood: '',
        lat: 0,
        lng: 0,
        city: '',
        cityIbgeCode: 0,
        stateAbbreviation: '',
      },
      useSecondAddress: false,
      secondAddress: {
        postalCode: '',
        address: '',
        number: '',
        complement: '',
        neighborhood: '',
        lat: 0,
        lng: 0,
        city: '',
        cityIbgeCode: 0,
        stateAbbreviation: '',
      },
      responsible_name: null,
    });
    expect(wrapper.vm.student).toMatchObject({
      grade: null,
      period: null,
      school: null,
      useSecondSchool: false,
      secondSchool: null,
      secondPeriod: null,
      match: null,
      student_name: null,
    });
    expect(wrapper.vm.newResponsible).toMatchObject({
      relationType: null,
      address: {
        postalCode: '',
        address: '',
        number: '',
        complement: '',
        neighborhood: '',
        lat: 0,
        lng: 0,
        city: '',
        cityIbgeCode: 0,
        stateAbbreviation: '',
      },
      useSecondAddress: false,
      secondAddress: {
        postalCode: '',
        address: '',
        number: '',
        complement: '',
        neighborhood: '',
        lat: 0,
        lng: 0,
        city: '',
        cityIbgeCode: 0,
        stateAbbreviation: '',
      },
      responsible_name: null,
    });
    expect(wrapper.vm.newStudent).toMatchObject({
      grade: null,
      period: null,
      school: null,
      useSecondSchool: false,
      secondSchool: null,
      secondPeriod: null,
      match: null,
      student_name: null,
    });
    expect(wrapper.vm.step).toBe('MATCH');
  });

  // test("if status isn't open, router push must be called to /", async () => {
  //   wrapper = await onMounted({
  //     stage: {
  //       ...stageMock,
  //       status: 'CLOSED',
  //     },
  //   });
  //
  //   await wrapper.vm.$nextTick();
  //
  //   expect(spyPush).toBeCalled();
  //   expect(spyPush).toBeCalledWith('/');
  // });
  //
  // test('if initial requisition crached, step has to be LOADING indefinitely', async () => {
  //   wrapper = await onMounted({
  //     stage: stageMock,
  //   }, true);
  //
  //   await wrapper.vm.$nextTick();
  //
  //   expect(wrapper.vm.step).toBe('LOADING');
  // });
  //
  // describe('prev', () => {
  //   beforeEach(async () => {
  //     wrapper = await onMounted();
  //
  //     await wrapper.vm.$nextTick();
  //   });
  //
  //   afterEach(() => {
  //     vi.clearAllMocks();
  //   });
  //
  //   test('step === RESPONSIBLE, must call router.back', async () => {
  //     wrapper.vm.step = 'RESPONSIBLE';
  //
  //     wrapper.vm.prev();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     expect(spyBack).toBeCalled();
  //     expect(spyBack).toBeCalledTimes(1);
  //   });
  //
  //   test('step === STUDENT, step must be mutate to RESPONSIBLE', async () => {
  //     wrapper.vm.step = 'STUDENT';
  //
  //     wrapper.vm.prev();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     expect(wrapper.vm.step).toBe('RESPONSIBLE');
  //   });
  //
  //   test('step === REVIEW, step must be mutate to STUDENT', async () => {
  //     wrapper.vm.step = 'REVIEW';
  //
  //     wrapper.vm.prev();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     expect(wrapper.vm.step).toBe('STUDENT');
  //   });
  // });
  //
  // describe('next', () => {
  //   beforeEach(async () => {
  //     wrapper = await onMounted();
  //
  //     await wrapper.vm.$nextTick();
  //   });
  //
  //   afterEach(() => {
  //     vi.clearAllMocks();
  //   });
  //
  //   test('step === MATCH, submitMatch function must be called', async () => {
  //     const spySubmitMatch = vi.spyOn(wrapper.vm.ctx, 'submitMatch');
  //
  //     vi.mocked(PreregistrationApi.postMatch).mockResolvedValueOnce({
  //       id: 1,
  //       grade: 'string',
  //       period: 'string',
  //       school: 'string',
  //       initials: 'string',
  //       dateOfBirth: 'string',
  //     });
  //
  //     wrapper.vm.step = 'MATCH';
  //
  //     await wrapper.vm.next();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     expect(spySubmitMatch).toBeCalled();
  //     expect(spySubmitMatch).toBeCalledTimes(1);
  //   });
  //
  //   test('step === RESPONSIBLE, step must be mutate to STUDENT', async () => {
  //     const spy = vi.spyOn(wrapper.vm.ctx, 'saveOnStorage');
  //
  //     wrapper.vm.step = 'RESPONSIBLE';
  //
  //     await wrapper.vm.next({
  //       address: {
  //         lat: 1,
  //         lng: 1,
  //       },
  //     });
  //
  //     await wrapper.vm.$nextTick();
  //
  //     expect(wrapper.vm.step).toBe('STUDENT');
  //     expect(spy).toBeCalled();
  //     expect(spy).toBeCalledTimes(1);
  //   });
  //
  //   test('step === RESPONSIBLE, but lat and lng is undefined step must continue to RESPONSIBLE', async () => {
  //     wrapper.vm.step = 'RESPONSIBLE';
  //
  //     await wrapper.vm.next({
  //       address: {},
  //     });
  //
  //     await wrapper.vm.$nextTick();
  //
  //     expect(wrapper.vm.step).toBe('RESPONSIBLE');
  //   });
  //
  //   test('step === STUDENT, submitToReview function must be called', async () => {
  //     const spySumitToReview = vi.spyOn(wrapper.vm.ctx, 'submitToReview');
  //     const spySaveOnStorage = vi.spyOn(wrapper.vm.ctx, 'saveOnStorage');
  //
  //     wrapper.vm.step = 'STUDENT';
  //
  //     await wrapper.vm.next();
  //
  //     expect(spySaveOnStorage).toBeCalledTimes(1);
  //     expect(spySumitToReview).toBeCalledTimes(1);
  //   });
  //
  //   test('step === REVIEW, submit function must be called', async () => {
  //     const spy = vi.spyOn(wrapper.vm.ctx, 'submit');
  //
  //     vi.mocked(PreregistrationApi.post).mockResolvedValueOnce({
  //       preregistrations: [
  //         {
  //           id: 1,
  //           protocol: 'string',
  //           code: 'string',
  //           type: 'string',
  //           date: '2022-06-02T00:00:00.000Z',
  //           position: 1,
  //           school: {
  //             id: 1,
  //             name: 'string',
  //             area_code: 'string',
  //             phone: 'string',
  //           },
  //           process: {
  //             showPriorityProtocol: true,
  //           },
  //         },
  //       ],
  //     });
  //
  //     wrapper.vm.step = 'REVIEW';
  //
  //     await wrapper.vm.next();
  //
  //     await flushPromises();
  //
  //     expect(spy).toBeCalledTimes(1);
  //   });
  // });
  //
  // describe('submitMatch', () => {
  //   beforeEach(async () => {
  //     wrapper = await onMounted();
  //
  //     await wrapper.vm.$nextTick();
  //   });
  //
  //   afterEach(() => {
  //     vi.clearAllMocks();
  //   });
  //
  //   test('submitMatch was be called successfully', async () => {
  //     const response = [
  //       {
  //         id: 1,
  //         initials: 'string',
  //         dateOfBirth: 'string',
  //         registration: {
  //           school: {
  //             id: 'string',
  //             name: 'string',
  //           },
  //           grade: {
  //             id: 'string',
  //             name: 'string',
  //           },
  //         },
  //       },
  //     ];
  //
  //     vi.mocked(PreregistrationApi.postMatch).mockResolvedValueOnce(response);
  //
  //     await wrapper.vm.submitMatch();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     await flushPromises();
  //
  //     expect(wrapper.vm.student.match).toMatchObject(response[0]);
  //   });
  //
  //   test('submitMatch was be called but has no data', async () => {
  //     vi.mocked(PreregistrationApi.postMatch).mockResolvedValueOnce([]);
  //
  //     await wrapper.vm.submitMatch();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     await flushPromises();
  //
  //     expect(wrapper.vm.student.match).toBe(undefined);
  //     expect(dialogSpy).toBeCalledWith({
  //       title: 'Aluno(a) não possui matrícula no ano passado',
  //       titleClass: 'danger',
  //       description: `Como o aluno não possui uma matrícula no ano passado não será possível seguir nesta opção. Clique em \'Voltar\' e efetue a inscrição em outro tipo de período.`,
  //     });
  //   });
  // });
  //
  // describe('submit', () => {
  //   beforeEach(async () => {
  //     wrapper = await onMounted();
  //
  //     await wrapper.vm.$nextTick();
  //   });
  //
  //   afterEach(() => {
  //     vi.clearAllMocks();
  //   });
  //
  //   test('submit was be called successfully', async () => {
  //     const response = {
  //       preregistrations: [],
  //     };
  //
  //     vi.mocked(PreregistrationApi.post).mockResolvedValueOnce(response);
  //
  //     await wrapper.vm.submit();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     await flushPromises();
  //
  //     expect(wrapper.vm.preregistrations).toMatchObject(
  //       response.preregistrations
  //     );
  //     expect(wrapper.vm.step).toMatchObject('PROTOCOL');
  //   });
  //
  //   test('submit was called with error', async () => {
  //     const err = {
  //       response: {
  //         data: {
  //           errors: [
  //             {
  //               message: 'Erro aconteceu',
  //               extensions: {
  //                 message: 'Erro aconteceu denovo',
  //               },
  //             },
  //           ],
  //           preregistrations: [],
  //         },
  //       },
  //     };
  //
  //     vi.mocked(PreregistrationApi.post).mockRejectedValue(err);
  //
  //     await wrapper.vm.submit();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     await flushPromises();
  //
  //     expect(dialogSpy).toBeCalledWith({
  //       title: err.response.data.errors[0].message,
  //       description: err.response.data.errors[0].extensions.message,
  //       titleClass: 'danger',
  //       iconLeft: 'status-red',
  //     });
  //   });
  // });
  //
  // describe('submitToReview', () => {
  //   beforeEach(async () => {
  //     wrapper = await onMounted();
  //
  //     await wrapper.vm.$nextTick();
  //   });
  //
  //   afterEach(() => {
  //     vi.clearAllMocks();
  //   });
  //
  //   test('submitToReview was be called successfully', async () => {
  //     wrapper.vm.step = 'STUDENT';
  //
  //     wrapper.vm.responsible = {
  //       responsible_cpf: '000.000.000-00',
  //       relationType: 'MOTHER',
  //     };
  //
  //     wrapper.vm.student = {
  //       student_cpf: '000.000.000-01',
  //       school: 1,
  //     };
  //
  //     wrapper.vm.submitToReview();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     expect(wrapper.vm.step).toBe('REVIEW');
  //   });
  //
  //   test('submitToReview was be called with conditional errors', async () => {
  //     wrapper.vm.step = 'STUDENT';
  //
  //     wrapper.vm.responsible = {
  //       responsible_cpf: '000.000.000-00',
  //       relationType: 'MOTHER',
  //       address: {
  //         lat: 0,
  //         lng: 0,
  //       },
  //     };
  //
  //     wrapper.vm.student = {
  //       student_cpf: '000.000.000-00',
  //     };
  //
  //     wrapper.vm.submitToReview();
  //
  //     await wrapper.vm.$nextTick();
  //
  //     expect(wrapper.vm.step).toBe('STUDENT');
  //   });
  // });
});
