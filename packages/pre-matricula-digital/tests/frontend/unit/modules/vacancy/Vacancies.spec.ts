import '@/plugin/sortby';
import {
  VacancyFilter,
  VacancyLoadProcesses,
  VacancyTotalProcess,
} from '@/modules/vacancy/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import VTooltip from 'v-tooltip';
import Vacancies from '@/modules/vacancy/components/Vacancies.vue';
import { Vacancy as VacancyApi } from '@/modules/vacancy/api';
import { createTestingPinia } from '@pinia/testing';
import { expect, vi } from 'vitest';

vi.mock('@/modules/vacancy/api');
vi.mock('vue3-apexcharts', () => ({
  render: () => '<div>Mocked ApexCharts</div>',
}));

const vacancyList: VacancyTotalProcess[] = [
  {
    schoolYear: {
      year: 2021,
    },
    totalPreRegistrations: 1217,
  },
  {
    schoolYear: {
      year: 2021,
    },
    totalPreRegistrations: 912,
  },
];

const vacancyLoad: VacancyLoadProcesses = {
  grades: [
    {
      id: '64',
      key: '64',
      label: 'Série - 4',
      name: 'Série - 4',
    },
  ],
  periods: [
    {
      id: '1',
      key: '1',
      label: 'Matutino',
      name: 'Matutino',
    },
  ],
  processes: [
    {
      id: '1',
      name: 'TESTE 1',
      unavailable: 0,
      exceded: 0,
      ultrapassed: 0,
      percentual: {
        accepted: 1,
        available: 1,
        unavailable: 1,
        waitingAvailable: 1,
        waitingExceded: 1,
        maxWaiting: 1,
        waitingRealAvailable: 1,
        total: 1,
        totalExceded: 1,
        exceded: 1,
        waiting: 1,
      },
      max: 1,
      totalExceded: 1,
      waitingAvailable: 1,
      waitingExceded: 1,
      maxWaiting: 1,
      process: {
        id: '1',
        name: 'TESTE 1',
      },
      accepted: 1,
      available: 1,
      excededVacancies: 0,
      rejected: 1,
      total: 1,
      waiting: 1,
    },
  ],
  schools: [
    {
      id: '00000',
      key: '00000',
      label: 'ESCOLA ESTADUAL DE ENSINO FUNDAMENTAL',
      name: 'ESCOLA ESTADUAL DE ENSINO FUNDAMENTAL',
    },
  ],
  stats: {
    vacancies: 0,
    total: 0,
    accepted: 0,
    rejected: 0,
    waiting: 0,
  },
  unique: {
    1: 1,
  },
};

const onMounted = async () => {
  vi.mocked(VacancyApi.list).mockResolvedValueOnce(vacancyList);
  vi.mocked(VacancyApi.load).mockResolvedValueOnce(vacancyLoad);

  const wrapper = mount(Vacancies, {
    global: {
      plugins: [createTestingPinia()],
      directives: {
        tooltip: VTooltip,
      },
      stubs: ['x-field'],
    },
  });

  await flushPromises();

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('Vacancies', () => {
  let wrapper: VueWrapper<InstanceType<typeof Vacancies>>;

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

  test('the initial variables must be loaded with the correct data', () => {
    expect(wrapper.vm.years).toEqual([
      {
        key: 2021,
        label: 2021,
      },
    ]);
    expect(wrapper.vm.filter).toEqual({
      school: '00000',
      period: null,
      grade: null,
      year: 2021,
      canAcceptInBatch: false,
    });

    expect(wrapper.vm.processes).toEqual(vacancyLoad.processes);
    expect(wrapper.vm.schools).toEqual(vacancyLoad.schools);
    expect(wrapper.vm.grades).toEqual(vacancyLoad.grades);
    expect(wrapper.vm.periods).toEqual(vacancyLoad.periods);
    expect(wrapper.vm.stats).toEqual(vacancyLoad.stats);

    expect(wrapper.vm.charts.length).toBe(1);
    expect(wrapper.vm.charts[0].series).toEqual([1, 1, 1]);
    expect(wrapper.vm.charts[0].id).toEqual('1');
  });

  test('checks the number of cards printed on the screen', () => {
    expect(wrapper.findAll('[data-test="vacancy-card"]').length).toBe(1);
  });

  test('Check if card shown on screen is printing data correctly', () => {
    const card = wrapper.find('[data-test="vacancy-card"]');

    expect(card.find('[data-test="vacancy-card-title"]').text()).toBe(
      'TESTE 1'
    );

    expect(card.find('[data-test="vacancy-card-relation"]').text()).toBe('1/1');
    expect(
      card.find('[data-test="vacancy-card-relation-percentage"]').text()
    ).toBe('100%');
  });

  test('When changing the filters, you must make a new listing of vacancies', async () => {
    vi.mocked(VacancyApi.load).mockResolvedValueOnce(vacancyLoad);

    const filter: VacancyFilter = {
      school: '00000',
      period: null,
      grade: null,
      year: 2021,
      canAcceptInBatch: false,
    };

    await wrapper.setValue(filter, 'filter');

    const btn = wrapper.find('[data-test="vacancy-btn-search"]');

    await btn.trigger('click');

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(wrapper.vm.filter).toEqual(filter);
    expect(wrapper.vm.charts.length).toBe(1);
    expect(wrapper.vm.charts[0].series).toEqual([1, 1, 1]);
    expect(wrapper.vm.charts[0].id).toEqual('1');
  });

  test('When clicking on the "clear" button, the Filter variable must have the original values', async () => {
    vi.mocked(VacancyApi.load).mockResolvedValueOnce(vacancyLoad);

    const btn = wrapper.find('[data-test="vacancy-btn-clear"]');

    await btn.trigger('click');

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(wrapper.vm.filter).toMatchObject({
      school: '00000',
      period: null,
      grade: null,
      year: null,
      canAcceptInBatch: false,
    });

    expect(wrapper.vm.charts.length).toBe(1);
    expect(wrapper.vm.charts[0].series).toEqual([1, 1, 1]);
    expect(wrapper.vm.charts[0].id).toEqual('1');
  });
});
