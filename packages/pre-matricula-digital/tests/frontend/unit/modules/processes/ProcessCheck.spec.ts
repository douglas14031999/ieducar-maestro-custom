import '@/plugin/sortby';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { ProcessCheck as IProcessCheck } from '@/modules/processes/types';
import { Process as ProcessApi } from '@/modules/processes/api';
import ProcessCheck from '@/modules/processes/components/ProcessCheck.vue';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import filters from '@/filters';
import { expect, vi } from 'vitest';
import { analytics } from '@/packages';
import { AnalyticsInstance } from 'analytics';

interface ProcessCheckProps {
  id?: string;
  newProcess: string;
}

vi.mock('@/modules/processes/api');
vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));
vi.mock('@/packages');

const spyPush = vi.fn();

const processCheckMock: IProcessCheck = {
  id: '1',
  name: 'Ed. Infantil (Nível III, Pré-Escola) Ens. Fundamental (1° ao 9° ano)',
  fields: [
    {
      field: {
        group: 'RESPONSIBLE',
        id: '1',
        internal: 'responsible_name',
        name: 'Nome do(a) responsável',
        options: [],
        type: 'TEXT',
      },
      id: '184',
      order: 1,
      required: true,
      weight: 0,
    },
  ],
  grades: [
    {
      endBirth: '2017-03-31',
      id: '21',
      name: 'Nível III',
      startBirth: '2016-03-31',
    },
  ],
  periods: [
    {
      id: '1',
      name: 'Matutino',
    },
  ],
  schoolYear: { year: 2021 },
  schools: [
    {
      id: '000000',
      latitude: 0,
      longitude: 0,
      name: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
    },
  ],
  stages: [
    {
      endAt: '2026-03-07 23:59:00',
      id: '17',
      name: 'Test',
      startAt: '2021-02-17 14:00:00',
      status: 'OPEN',
      type: 'REGISTRATION',
    },
  ],
  vacancies: [
    {
      available: 54,
      grade: '6',
      period: '2',
      school: '98370',
      total: 81,
    },
  ],
};

const onMounted = async (
  props: ProcessCheckProps,
  processCheck: {
    success: boolean;
    data: IProcessCheck;
  }
) => {
  if (processCheck.success) {
    vi.mocked(ProcessApi.show).mockResolvedValueOnce(processCheck.data);
  } else {
    vi.mocked(ProcessApi.show).mockRejectedValueOnce(processCheck.data);
  }

  vi.mocked(analytics).mockImplementation(
    () =>
      ({
        page: vi.fn(),
      } as unknown as AnalyticsInstance)
  );

  const wrapper = mount(ProcessCheck, {
    props,
    global: {
      plugins: [createTestingPinia()],
      stubs: ['x-field'],
      provide: {
        $filters: filters,
      },
      directives: {
        tooltip: VTooltip,
      },
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('ProcessCheck', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProcessCheck>>;

  beforeEach(async () => {
    wrapper = await onMounted(
      {
        newProcess: 'false',
        id: '1',
      },
      {
        success: true,
        data: processCheckMock,
      }
    );

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
    expect(wrapper.vm.student).toMatchObject({
      grade: null,
      period: null,
      school: null,
      useSecondSchool: false,
      secondSchool: null,
      secondPeriod: null,
      match: null,
    });
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
    expect(wrapper.vm.fields.responsible.length).toBe(1);
    expect(wrapper.vm.fields.student.length).toBe(0);
  });

  describe('Error on initial requisition', () => {
    beforeEach(async () => {
      wrapper = await onMounted(
        {
          newProcess: 'false',
          id: '1',
        },
        {
          success: false,
          data: {} as IProcessCheck,
        }
      );

      await wrapper.vm.$nextTick();
    });

    afterEach(() => {
      vi.clearAllMocks();
    });

    test('the initial variables must be loaded with the correct data', () => {
      expect(wrapper.vm.student).toMatchObject({});
      expect(wrapper.vm.responsible).toMatchObject({});
      expect(wrapper.vm.fields.responsible.length).toBe(0);
      expect(wrapper.vm.fields.student.length).toBe(0);
    });
  });

  test('if I click on "go back" button, the user should be redirected to previous page', async () => {
    const button = wrapper.find('[data-test="btn-back"]');

    await button.trigger('click');

    expect(spyPush).toBeCalled();
    expect(spyPush).toBeCalledTimes(1);
    expect(spyPush).toBeCalledWith({
      name: 'process.periods',
      params: {
        id: '1',
        newProcess: 'false',
      },
    });
  });

  describe('submit', () => {
    test('if I click on "submit" button, the user should be redirected to next page', async () => {
      const button = wrapper.find('[data-test="btn-submit"]');

      await button.trigger('click');

      expect(spyPush).toBeCalled();
      expect(spyPush).toBeCalledTimes(1);
      expect(spyPush).toBeCalledWith({
        name: 'processes',
      });
    });
  });
});
