import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Process as ProcessApi } from '@/modules/processes/api';
import { ProcessGetList } from '@/modules/processes/types';
import ProcessList from '@/modules/processes/components/ProcessList.vue';
import VTooltip from 'v-tooltip';
import filters from '@/filters';
import { expect, vi } from 'vitest';

vi.mock('@/modules/processes/api');
vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));

const spyPush = vi.fn();

const processMock: ProcessGetList = {
  processes: [
    {
      active: true,
      id: '1',
      key: '1',
      label: 'Educação infantil (Berçário a Nível II)',
      name: 'Educação infantil (Berçário a Nível II)',
      schoolYear: {
        year: 2021,
      },
      stages: [
        {
          id: '1',
          type: 'REGISTRATION_RENEWAL',
          name: 'Test',
          status: 'OPEN',
          startAt: '2020-10-14 00:00:00',
          endAt: '2022-10-19 23:59:00',
        },
      ],
    },
  ],
  years: [
    {
      key: 2021,
      label: '2021',
    },
  ],
  status: [
    {
      key: 'OPEN',
      label: 'EM ANDAMENTO',
    },
  ],
};

const onMounted = async (process: ProcessGetList, error = false) => {
  if (!error) {
    vi.mocked(ProcessApi.getList).mockResolvedValueOnce(process);
  } else {
    vi.mocked(ProcessApi.getList).mockRejectedValueOnce(process);
  }

  const wrapper = mount(ProcessList, {
    global: {
      provide: {
        $filters: filters,
      },
      stubs: ['router-link', 'x-card-section'],
      directives: {
        tooltip: VTooltip,
      },
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('ProcessList', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProcessList>>;

  beforeEach(async () => {
    wrapper = await onMounted(processMock);

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
    expect(wrapper.vm.processes).toMatchObject(processMock.processes);
    expect(wrapper.vm.years).toMatchObject(processMock.years);
    expect(wrapper.vm.status).toMatchObject(processMock.status);
    expect(wrapper.vm.filteredProcesses).toMatchObject(processMock.processes);
    expect(wrapper.vm.filter).toMatchObject({
      year: null,
      status: null,
      id: null,
    });
  });

  test('should render 1 process on the screen', () => {
    const processesList = wrapper.findAll('[data-test="process-block"]');

    expect(processesList.length).toBe(1);
  });

  test('should render 1 stage on the screen', () => {
    const stagesList = wrapper.findAll('[data-test="stage-card"]');

    expect(stagesList.length).toBe(1);
  });

  test('when I click on "new process" button, should redirect to the new process page', async () => {
    const newProcessButton = wrapper.find('[data-test="new-process-button"]');

    newProcessButton.trigger('click');

    await flushPromises();

    expect(spyPush).toBeCalledTimes(1);
    expect(spyPush).toBeCalledWith({
      name: 'process.create',
      params: { newProcess: 'true' },
    });
  });

  test('should empty initial variables if occurs an error in the api', async () => {
    wrapper = await onMounted(
      {
        processes: [],
        years: [],
        status: [],
      },
      true
    );

    await wrapper.vm.$nextTick();

    expect(wrapper.vm.processes).toMatchObject([]);
    expect(wrapper.vm.years).toMatchObject([]);
    expect(wrapper.vm.status).toMatchObject([]);
    expect(wrapper.vm.filteredProcesses).toMatchObject([]);
    expect(wrapper.vm.filter).toMatchObject({
      year: null,
      status: null,
      id: null,
    });
  });
});
