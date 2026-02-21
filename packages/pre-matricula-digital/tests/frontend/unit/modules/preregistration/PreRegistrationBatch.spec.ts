import '@/plugin/sortby';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { expect, vi } from 'vitest';
import { useRoute, useRouter } from 'vue-router';
import {Nullable, Option} from '@/types';
import { PreRegistrationBatchProps } from '@/modules/preregistration/types';
import PreRegistrationBatch from '@/modules/preregistration/components/PreRegistrationBatch.vue';
import { Preregistration as PreregistrationApi } from '@/modules/preregistration/api';
import filters from '@/filters';

const spyPush = vi.fn();

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

const propsMock: PreRegistrationBatchProps = {
  filter: {
    canAcceptInBatch: false,
    grade: 'string',
    period: 'string',
    process: 'string',
    school: 'string',
    schools: [],
    search: 'string',
    sort: 'string',
    status: 'string',
    type: 'string',
    year: 2021,
    processes: [],
  },
  preregistrations: ['1'],
  step: 'SUMMON',
  processYear: 2022,
  modelValue: true,
};

const getClassroomsByPreregistrationMock: Option[] = [
  {
    key: 1,
    label: 'string',
  },
];

const onMounted = async (
  getClassroomsByPreregistration = getClassroomsByPreregistrationMock,
  props = propsMock
) => {
  vi.mocked(
    PreregistrationApi.getClassroomsByPreregistration
  ).mockResolvedValueOnce(getClassroomsByPreregistration);

  const wrapper = mount(PreRegistrationBatch, {
    props,
    global: {
      provide: {
        $filters: filters,
      },
      stubs: ['x-field', 'modal'],
    },
  });

  await wrapper.vm.$nextTick();
  await flushPromises();

  return wrapper;
};

describe('PreRegistrationBatch', () => {
  let wrapper: VueWrapper<InstanceType<typeof PreRegistrationBatch>>;

  beforeEach(async () => {
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

  describe('accept', () => {
    test('should return with success response', async () => {
      vi.mocked(PreregistrationApi.postAcceptBatch).mockResolvedValueOnce({
        acceptPreRegistrations: [
          {
            id: '1',
            student: {
              name: 'Aluno 01',
            },
          },
          {
            id: '2',
            student: {
              name: 'Aluno 02',
            },
          },
        ],
      });

      wrapper.vm.classroom = 123;

      wrapper.vm.accept();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(wrapper.vm.success).toBeTruthy();
      expect(wrapper.vm.total).toBe(2);
      expect(wrapper.vm.loading).toBeFalsy();
    });

    test('should return with fail response', async () => {
      const errors = [
        {
          message: 'Algo deu errado.',
          extensions: {
            message: 'Erro ao invocar a função.',
          },
        },
      ];

      vi.mocked(PreregistrationApi.postAcceptBatch).mockRejectedValueOnce({
        response: {
          data: {
            errors: errors,
            data: {
              acceptPreRegistrations: [],
            },
          },
        },
      });

      wrapper.vm.classroom = 123;

      wrapper.vm.accept();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(wrapper.vm.showError).toBeTruthy();
      expect(wrapper.vm.errorTitle).toBe(
        'Não foi possível realizar o deferimento de todos os(as) alunos(as)'
      );
      expect(wrapper.vm.error).toBe(errors[0].extensions.message);
      expect(wrapper.vm.loading).toBeFalsy();
    });

    test('should show empty classroom message if none classroom was provided', async () => {
      const errors = [
        {
          message: 'Algo deu errado.',
          extensions: {
            message: 'Erro ao invocar a função.',
          },
        },
      ];

      vi.mocked(PreregistrationApi.postAcceptBatch).mockRejectedValueOnce({
        response: {
          data: {
            errors: errors,
            data: {
              acceptPreRegistrations: [],
            },
          },
        },
      });

      wrapper.vm.accept();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(wrapper.vm.showError).toBeTruthy();
      expect(wrapper.vm.errorTitle).toBe(
        'É necessário selecionar uma turma para realizar o Deferimento da pré-matrícula.'
      );
      expect(wrapper.vm.error).toBe('');
      expect(wrapper.vm.loading).toBeFalsy();
    });
  });

  describe('summon', () => {
    test('should return with success response', async () => {
      vi.mocked(PreregistrationApi.postSummonBatch).mockResolvedValueOnce({
        summonPreRegistrations: [
          {
            id: '1',
            student: {
              name: 'Aluno 01',
            },
          },
          {
            id: '2',
            student: {
              name: 'Aluno 02',
            },
          },
        ],
      });

      wrapper.vm.summon();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(wrapper.vm.success).toBeTruthy();
      expect(wrapper.vm.total).toBe(2);
      expect(wrapper.vm.loading).toBeFalsy();
    });

    test('should return with fail response', async () => {
      const errors = [
        {
          message: 'Algo deu errado.',
          extensions: {
            message: 'Erro ao invocar a função.',
          },
        },
      ];

      vi.mocked(PreregistrationApi.postSummonBatch).mockRejectedValueOnce({
        response: {
          data: {
            errors: errors,
            data: {
              summonPreRegistrations: [],
            },
          },
        },
      });

      wrapper.vm.summon();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(wrapper.vm.showError).toBeTruthy();
      expect(wrapper.vm.errorTitle).toBe(
        'Não foi possível realizar a convocação de todos os(as) responsáveis(as)'
      );
      expect(wrapper.vm.error).toBe(errors[0].extensions.message);
      expect(wrapper.vm.loading).toBeFalsy();
    });
  });

  describe('reject', () => {
    test('should return with success response', async () => {
      vi.mocked(PreregistrationApi.postRejectBatch).mockResolvedValueOnce({
        rejectPreRegistrations: [
          {
            id: '1',
            student: {
              name: 'Aluno 01',
            },
          },
          {
            id: '2',
            student: {
              name: 'Aluno 02',
            },
          },
        ],
      });

      wrapper.vm.reject();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(wrapper.vm.success).toBe(true);
      expect(wrapper.vm.total).toBe(2);
      expect(wrapper.vm.loading).toBeFalsy();
    });

    test('should return with fail response', async () => {
      const errors = [
        {
          message: 'Algo deu errado.',
          extensions: {
            message: 'Erro ao invocar a função.',
          },
        },
      ];

      vi.mocked(PreregistrationApi.postRejectBatch).mockRejectedValueOnce({
        errors: errors,
      });

      wrapper.vm.reject();

      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(wrapper.vm.showError).toBeTruthy();
      expect(wrapper.vm.errorTitle).toBe('Algo deu errado.');
      expect(wrapper.vm.error).toBe(errors[0].extensions.message);
      expect(wrapper.vm.loading).toBeFalsy();
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
});
