import '@/plugin/sortby';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { VacancyListByGrades as IVacancyListByGrades } from '@/modules/vacancy/types';
import VTooltip from 'v-tooltip';
import VacanciesByGrades from '@/components/charts/VacanciesByGrades.vue';
import { Vacancy as VacancyApi } from '@/modules/vacancy/api';
import { expect, vi } from 'vitest';

vi.mock('@/modules/vacancy/api');

const VacancyListByGrades: IVacancyListByGrades = {
  grades: [
    {
      id: '1',
      name: 'TESTE',
      total: 1,
      available: 1,
      waiting: 1,
      accepted: 1,
      rejected: 1,
      exceded: false,
    },
  ],
  statistics: [
    {
      accepted: 1,
      available: 1,
      grade: '1',
      period: '1',
      rejected: 1,
      school: '1',
      total: 1,
      waiting: 1,
    },
  ],
};

const onMounted = async () => {
  const wrapper = mount(VacanciesByGrades, {
    global: {
      directives: {
        tooltip: VTooltip,
      },
      stubs: ['x-field'],
    },
    props: {
      process: '1',
      filter: {
        school: '1',
        period: 1,
        grade: 1,
        year: 2021,
        canAcceptInBatch: false,
      },
    },
  });

  await flushPromises();

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('VacanciesByGrades', () => {
  let wrapper: VueWrapper<InstanceType<typeof VacanciesByGrades>>;

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

  test('When I click on the "see more details" button, it should open more data about the card', async () => {
    vi.mocked(VacancyApi.listByGrades).mockResolvedValueOnce(
      VacancyListByGrades
    );

    const button = wrapper.find('[data-test="vacancy-card-see-more"]');

    button.trigger('click');

    await wrapper.vm.$nextTick();

    await flushPromises();

    const details = wrapper.findAll('[data-test="vacancy-by-grades-detail"]');

    expect(details.length).toBe(1);

    expect(wrapper.vm.open).toBe(true);

    expect(wrapper.vm.grades).toMatchObject(VacancyListByGrades.grades);

    expect(details.at(0)?.findAll('.d-inline-block').length).toBe(1);
  });

  test('Open should be false when there is an error on the server', async () => {
    vi.mocked(VacancyApi.listByGrades).mockRejectedValueOnce({});

    wrapper = await onMounted();

    await wrapper.vm.$nextTick();

    const button = wrapper.find('[data-test="vacancy-card-see-more"]');

    button.trigger('click');

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(wrapper.vm.open).toBe(false);
    expect(wrapper.vm.grades).toMatchObject([]);
  });

  test('When function details with pararms false is active, should return call the function load', async () => {
    vi.mocked(VacancyApi.listByGrades).mockResolvedValueOnce(
      VacancyListByGrades
    );
    const spyLoad = vi.spyOn(wrapper.vm.ctx, 'load');

    wrapper.vm.details(false);

    await wrapper.vm.$nextTick();

    expect(spyLoad).toBeCalledTimes(1);
  });

  test('When function details with pararms true is active, should return grades empty and open false', async () => {
    wrapper.vm.details(true);

    await wrapper.vm.$nextTick();
    expect(wrapper.vm.grades).toStrictEqual([]);
    expect(wrapper.vm.open).toBeFalsy();
  });
});
