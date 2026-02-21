import '@/plugin/sortby';
import { VueWrapper, shallowMount } from '@vue/test-utils';
import ProcessStages from '@/components/elements/ProcessStages.vue';
import { Processes } from '@/types';
import { expect, vi } from 'vitest';

interface Params {
  error: boolean;
}

interface ProcessesParams extends Params {
  process: Processes;
}

const processMock = {
  id: 'string',
  name: 'string',
  schoolYear: {
    year: 2021,
  },
  stages: [
    {
      endAt: '2020-10-19 23:59:00',
      id: '1',
      name: 'Test',
      startAt: '2020-10-14 00:00:00',
      status: 'CLOSED',
      type: 'REGISTRATION_RENEWAL',
    },
  ],
  grades: [
    {
      id: 27,
      name: 'Nível I',
    },
  ],
};

const onMounted = async (processesParams: ProcessesParams) => {
  const wrapper = shallowMount(ProcessStages, {
    props: {
      process: processesParams.process,
    },
  });

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('ProcessStages', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProcessStages>>;

  beforeEach(async () => {
    wrapper = await onMounted({
      error: false,
      process: processMock,
    });

    await wrapper.vm.$nextTick();
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  test('the component exists', () => {
    expect(wrapper).toBeTruthy();
  });

  test('the initial variables must be loaded with the correct data', () => {
    expect(wrapper.vm.process).toMatchObject(processMock);
    expect(wrapper.vm.stages).toMatchObject(processMock.stages);
  });

  test('the component must render the correct data', () => {
    const component = wrapper.findAllComponents('process-stage-card-stub');

    expect(component.length).toBe(1);
    expect(component.at(0)?.exists()).toBeTruthy();
  });

  test('if process is empty, the component must not render', async () => {
    wrapper = await onMounted({
      error: true,
      process: {} as Processes,
    });

    const component = wrapper.findAllComponents('process-stage-card-stub');

    expect(wrapper.vm.process).toMatchObject({});
    expect(wrapper.vm.stages).toMatchObject([]);

    expect(component.length).toBe(0);
    expect(component.at(0)?.exists()).toBeFalsy();
  });
});
