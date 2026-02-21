import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Process as ProcessApi } from '@/modules/processes/api';
import { ProcessStageResponse } from '@/modules/processes/types';
import ProcessStages from '@/modules/processes/components/ProcessStages.vue';
import { createTestingPinia } from '@pinia/testing';
import { expect, vi } from 'vitest';
import { analytics } from '@/packages';
import { AnalyticsInstance } from 'analytics';

interface ProcessStagesProps {
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

const processMock: ProcessStageResponse = {
  process: {
    id: '1',
    name: 'Ed. Infantil (Nível III, Pré-Escola) Ens. Fundamental (1° ao 9° ano)',
  },
  stages: [
    {
      allowWaitingList: true,
      endAt: '2021-03-07 23:59:00',
      endHourAt: '2021-03-07 23:59:00',
      id: '1',
      observation: '',
      radius: 1500,
      renewalAtSameSchool: false,
      startAt: '2021-02-17 14:00:00',
      startHourAt: '2021-02-17 14:00:00',
      type: 'REGISTRATION',
      allowSearch: false,
      restrictionType: 'NONE',
    },
  ],
};

const onMounted = async (props: ProcessStagesProps) => {
  vi.mocked(ProcessApi.listStages).mockResolvedValueOnce(processMock);

  vi.mocked(analytics).mockImplementation(
    () =>
      ({
        page: vi.fn(),
      } as unknown as AnalyticsInstance)
  );

  const wrapper = mount(ProcessStages, {
    props,
    global: {
      plugins: [createTestingPinia()],
      stubs: ['x-field'],
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('ProcessStages', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProcessStages>>;

  beforeEach(async () => {
    wrapper = await onMounted({
      newProcess: 'false',
      id: '1',
    });

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
    expect(wrapper.vm.stages).toMatchObject(processMock.stages);
    expect(wrapper.vm.data).toMatchObject(processMock);
  });

  describe('addStage', () => {
    test('should call addStage method', async () => {
      wrapper.vm.addStage = vi.fn();

      wrapper.vm.addStage();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.addStage).toHaveBeenCalled();
      expect(wrapper.vm.addStage).toHaveBeenCalledTimes(1);
    });

    test('should call addStage method and push the stages array', async () => {
      expect(wrapper.vm.stages).toHaveLength(1);

      wrapper.vm.addStage();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.stages).toHaveLength(2);
    });
  });

  describe('removeStage', () => {
    test('should call removeStage method', async () => {
      wrapper.vm.removeStage = vi.fn();

      wrapper.vm.removeStage();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.removeStage).toHaveBeenCalled();
      expect(wrapper.vm.removeStage).toHaveBeenCalledTimes(1);
    });

    test('should call removeStage method and remove 1 item of the stages array', async () => {
      expect(wrapper.vm.stages).toHaveLength(2);

      wrapper.vm.removeStage();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.removeStage).toHaveLength(1);
    });
  });

  test('if I click on "go back" button, the user should be redirected to previous page', async () => {
    const button = wrapper.find('[data-test="button-back"]');

    await button.trigger('click');

    expect(spyPush).toBeCalled();
    expect(spyPush).toBeCalledTimes(1);
    expect(spyPush).toBeCalledWith({
      name: 'process.fields',
      params: {
        id: '1',
        newProcess: 'false',
      },
    });
  });

  describe('submit', () => {
    test('should call submit method successfully', async () => {
      vi.mocked(ProcessApi.postStages).mockResolvedValueOnce();

      wrapper.vm.submit();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(spyPush).toHaveBeenCalledWith({
        name: 'process.check',
        params: {
          id: '1',
          newProcess: 'false',
        },
      });
    });

    test('should call submit method unsuccessfully', async () => {
      vi.mocked(ProcessApi.postStages).mockRejectedValueOnce();

      wrapper.vm.submit();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(spyPush).not.toHaveBeenCalled();
    });
  });
});
