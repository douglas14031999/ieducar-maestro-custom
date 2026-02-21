import '@/plugin/sortby';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { expect, vi } from 'vitest';
import { useRoute, useRouter } from 'vue-router';
import { Option } from '@/types';
import { PreRegistration } from '@/modules/preregistration/types';
import PreRegistrationModal from '@/modules/preregistration/components/PreRegistrationModal.vue';
import { Preregistration as PreregistrationApi } from '@/modules/preregistration/api';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import filters from '@/filters';

vi.mock('@/modules/preregistration/api');
vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
  useRoute: () => ({
    params: {
      protocol: 'HLYN3T',
    },
  }),
}));

const spyPush = vi.fn();

const loaderByProtocolMock: PreRegistration = {
  id: 1,
  date: '2020-11-17 00:02:40',
  protocol: 'HLYN3T',
  status: 'REJECTED',
  type: 'REGISTRATION',
  observation: '',
  grade: {
    id: '1',
    name: '1º Ano',
    course: {
      name: 'Ensino Fundamental 9 anos',
    },
  },
  period: {
    id: '1',
    name: 'Vespertino',
  },
  school: {
    id: '1',
    name: 'Escola 001',
  },
  classroom: {
    id: '1',
    name: '67',
  },
  position: 4,
  waiting: {
    id: '1',
    protocol: 'HLYN3T',
    school: {
      id: '1',
      name: 'Escola 001',
    },
    position: 4,
  },
  parent: {
    id: '1',
    protocol: 'HLYN3T',
    school: {
      id: '1',
      name: 'Escola 001',
    },
    position: 4,
  },
  student: {
    student_birth_certificate: 'null',
    student_cpf: 'null',
    student_date_of_birth: '2014-12-06',
    student_email: 'null',
    student_gender: 1,
    student_marital_status: 1,
    student_mobile: 'null',
    student_name: 'Daenerys Targarien',
    student_phone: 'null',
    student_place_of_birth: 4,
    student_rg: 'null',
  },
  responsible: {
    responsible_address: [],
    responsible_cpf: '107.247.559-69',
    responsible_date_of_birth: '1996-06-03',
    responsible_email: 'null',
    responsible_gender: 1,
    responsible_marital_status: 1,
    responsible_mobile: 'null',
    responsible_name: 'Jhon Snow',
    responsible_phone: '(99) 99999-9999',
    responsible_place_of_birth: 2,
    responsible_rg: 'null',
  },
  relationType: 'MOTHER',
  fields: [
    {
      id: '1',
      value: '18',
      field: {
        id: '19',
        name: 'Aluno possui algum tipo de deficiência/ Síndrome?',
        internal: 'null',
        group: 'STUDENT',
      },
    },
    {
      id: '2',
      value: '18',
      field: {
        id: '20',
        name: 'Aluno possui algum tipo de deficiência/ Síndrome?',
        internal: 'null',
        group: 'RESPONSIBLE',
      },
    },
  ],
  process: {
    id: '1',
    name: 'Ensino Fundamental (1º ao 9º ano)',
    fields: [
      {
        id: '13',
        order: 7,
        field: {
          id: 'string',
          name: 'string',
          group: 'string',
          internal: 'responsible_rg',
          type: 'GENDER',
          options: [
            {
              id: 'string',
              key: 'string',
              name: 'string',
              label: 'string',
              weight: 0,
            },
          ],
        },
        required: true,
        weight: 0,
      },
    ],
    schoolYear: {
      year: 20211,
    },
  },
  inClassroom: null,
  others: [],
};

const getClassroomsByPreregistrationMock: Option[] = [
  {
    key: 1,
    label: 'string',
  },
];

const onMounted = async (
  loaderByProtocol = loaderByProtocolMock,
  getClassroomsByPreregistration = getClassroomsByPreregistrationMock
) => {
  vi.mocked(PreregistrationApi.listByProtocol).mockResolvedValueOnce(
    loaderByProtocol
  );
  vi.mocked(
    PreregistrationApi.getClassroomsByPreregistration
  ).mockResolvedValueOnce(getClassroomsByPreregistration);

  const wrapper = mount(PreRegistrationModal, {
    global: {
      provide: {
        $filters: filters,
      },
      plugins: [createTestingPinia()],
      stubs: ['x-field'],
      directives: {
        tooltip: VTooltip,
      },
    },
  });

  await wrapper.vm.$nextTick();
  await flushPromises();

  return wrapper;
};

describe('PreRegistrationModal', () => {
  let wrapper: VueWrapper<InstanceType<typeof PreRegistrationModal>>;

  beforeEach(async () => {
    wrapper = await onMounted(loaderByProtocolMock);

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

  describe('accept', () => {
    test('should return with success response', async () => {
      vi.mocked(PreregistrationApi.postAccept).mockResolvedValueOnce(undefined);

      wrapper.vm.step = 'ACCEPT';
      wrapper.vm.classroom = 123;
      wrapper.vm.accept();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(spyPush).toHaveBeenCalledTimes(1);
      expect(spyPush).toHaveBeenCalledWith({
        name: 'preregistrations',
      });
    });

    test('should return with fail response', async () => {
      const errors = [
        {
          message: 'string',
          extensions: {
            message: 'string',
          },
        },
      ];

      vi.mocked(PreregistrationApi.postAccept).mockRejectedValueOnce({
        response: {
          data: {
            errors,
          },
        },
      });

      wrapper.vm.step = 'ACCEPT';
      wrapper.vm.classroom = 123;
      wrapper.vm.accept();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(spyPush).not.toHaveBeenCalled();
      expect(wrapper.vm.showError).toBeTruthy();
      expect(wrapper.vm.errorTitle).toBe(errors[0].message);
      expect(wrapper.vm.error).toBe(errors[0].extensions.message);
    });

    test('should show error message when classroom is undefined', async () => {
      const errors = [
        {
          message: 'string',
          extensions: {
            message: 'string',
          },
        },
      ];

      vi.mocked(PreregistrationApi.postAccept).mockRejectedValueOnce({
        response: {
          data: {
            errors,
          },
        },
      });

      wrapper.vm.step = 'ACCEPT';
      wrapper.vm.classroom = undefined;
      wrapper.vm.accept();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(spyPush).not.toHaveBeenCalled();
      expect(wrapper.vm.showError).toBeTruthy();
      expect(wrapper.vm.errorTitle).toBe(
        'É necessário selecionar uma turma para realizar o Deferimento da pré-matrícula.'
      );
      expect(wrapper.vm.error).toBe('');
    });
  });

  describe('summon', () => {
    test('should return with success response', async () => {
      vi.mocked(PreregistrationApi.postSummon).mockResolvedValueOnce(undefined);

      wrapper.vm.step = 'SUMMON';
      wrapper.vm.summon();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(spyPush).toHaveBeenCalledTimes(1);
      expect(spyPush).toHaveBeenCalledWith({
        name: 'preregistrations',
      });
    });

    test('should return with fail response', async () => {
      const errors = [
        {
          message: 'string',
          extensions: {
            message: 'string',
          },
        },
      ];

      vi.mocked(PreregistrationApi.postSummon).mockRejectedValueOnce({
        response: {
          data: {
            errors,
          },
        },
      });

      wrapper.vm.step = 'SUMMON';
      wrapper.vm.summon();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(spyPush).not.toHaveBeenCalled();
      expect(wrapper.vm.showError).toBeTruthy();
      expect(wrapper.vm.errorTitle).toBe(
        'Não foi possível realizar a convocação de todos os(as) responsáveis(as)'
      );
      expect(wrapper.vm.error).toBe(errors[0].extensions.message);
    });
  });

  describe('go', () => {
    test('should return redirect', () => {
      wrapper.vm.go('HLYN3T');

      expect(spyPush).toHaveBeenCalledWith({
        name: 'preregistration.modal',
        params: {
          protocol: 'HLYN3T',
        },
      });
    });
  });

  describe('getClassroomsByPreregistration', () => {
    test('should call the getClassroomsByPreregistration method with the correct params', async () => {
      vi.mocked(
        PreregistrationApi.getClassroomsByPreregistration
      ).mockResolvedValueOnce(getClassroomsByPreregistrationMock);
      wrapper.vm.getClassroomsByPreregistration();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(wrapper.vm.classrooms).toMatchObject(
        getClassroomsByPreregistrationMock
      );
    });
  });

  describe('returnToWait', () => {
    test('should call the returnToWait method with the correct params', async () => {
      vi.mocked(PreregistrationApi.postReturnToWait).mockResolvedValueOnce(
        undefined
      );

      wrapper.vm.returnToWait();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(spyPush).toHaveBeenCalledWith({
        name: 'preregistrations',
      });
    });
  });

  describe('reject', () => {
    test('should call the reject method with the correct params', async () => {
      vi.mocked(PreregistrationApi.postReject).mockResolvedValueOnce(undefined);
      wrapper.vm.step = 'REJECT';
      wrapper.vm.reject();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(spyPush).toHaveBeenCalledWith({
        name: 'preregistrations',
      });
    });

    test('should return justification empty string', () => {
      wrapper.vm.reject();
      wrapper.vm.step = 'SHOW';
      expect(wrapper.vm.justification).toBe('');
    });
  });
});
