import '@/plugin/sortby';
import {
  StatProcesses,
  StatProcessesProps,
} from '@/modules/preregistration/types';

import { VueWrapper, flushPromises, shallowMount } from '@vue/test-utils';
import StatsCards from '@/components/elements/StatsCards.vue';
import XCard from '@/components/elements/cards/XCard.vue';

import { expect, vi } from 'vitest';

const propsMock: StatProcessesProps = {
  stats: {
    accepted: 3197,
    rejected: 1227,
    total: 4890,
    vacancies: 25518,
    waiting: 466,
  },
};

const statesCardsMock = [
  {
    quantity: 25518,
    text: 'Vagas ofertadas',
    bgColor: 'bg-blue svg-real-estate',
    cardClass: 'col-6 col-md-2',
  },
  {
    quantity: 4890,
    text: 'Pré-matrículas inscritas',
    bgColor: 'bg-blue svg-perfis',
    cardClass: 'col-6 col-md-2',
  },
  {
    quantity: 3197,
    text: 'Deferidas',
    bgColor: 'bg-green svg-aprovado',
    cardClass: 'col-6 col-md-2',
  },
  {
    quantity: 1227,
    text: 'Indeferidas',
    bgColor: 'bg-red svg-desaprovado',
    cardClass: 'col-6 col-md-2',
  },
  {
    quantity: 466,
    text: 'Em espera',
    bgColor: 'bg-yellow svg-esperando',
    cardClass: 'col-6 col-md-2',
  },
];

const onMounted = async (props = propsMock) => {
  const wrapper = shallowMount(StatsCards, {
    props,
  });

  await wrapper.vm.$nextTick();
  await flushPromises();

  return wrapper;
};

describe('StatsCards', () => {
  let wrapper: VueWrapper<InstanceType<typeof StatsCards>>;

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

  test('the initial variables must be loaded with the correct data', () => {
    expect(wrapper.vm.stats).toMatchObject(propsMock.stats);
    expect(wrapper.vm.statsCards).toMatchObject(statesCardsMock);
  });

  test('should match snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  test('the component must render the correct data', () => {
    const component = wrapper.findAllComponents({
      name: 'x-card',
    });

    expect(component).toBeTruthy();
    expect(component.length).toBe(5);
  });

  test('if process is empty, the component must not render', async () => {
    wrapper = await onMounted({
      stats: {} as StatProcesses,
    });

    const component = wrapper.findAllComponents({
      name: 'x-card',
    });

    await wrapper.vm.$nextTick();
    await flushPromises();

    expect(wrapper.vm.stats).toMatchObject({});
    expect(wrapper.vm.statsCards).toMatchObject([]);
    expect(component.length).toBe(0);
  });
});
