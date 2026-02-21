import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { ProcessField as ProcessFieldApi } from '@/modules/processes/api';
import { ProcessFieldList } from '@/modules/processes/types';
import ProcessFields from '@/modules/processes/components/ProcessFields.vue';
import VTooltip from 'v-tooltip';
import { expect, vi } from 'vitest';
import { AnalyticsInstance } from 'analytics';
import { analytics } from '@/packages';

interface ProcessFieldsProps {
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

const processFieldMock: ProcessFieldList = {
  process: {
    fields: [
      { field: { id: '1' }, required: true, weight: 0, order: 1 },
      { field: { id: '3' }, required: true, weight: 0, order: 2 },
      { field: { id: '4' }, required: false, weight: 0, order: 3 },
      { field: { id: '2' }, required: true, weight: 0, order: 4 },
      { field: { id: '6' }, required: false, weight: 0, order: 5 },
    ],
    id: '1',
    name: 'Ed. Infantil (Nível III, Pré-Escola) Ens. Fundamental (1° ao 9° ano)',
  },
  responsibleFields: [
    {
      checked: true,
      group: 'RESPONSIBLE',
      id: '1',
      internal: 'responsible_name',
      mandatory: true,
      name: 'Nome do(a) responsável',
      open: false,
      options: [],
      order: 1,
      priority: false,
      required: true,
      type: 'TEXT',
      weight: 0,
    },
  ],
  studentFields: [
    {
      checked: true,
      group: 'STUDENT',
      id: '9',
      internal: 'student_name',
      mandatory: true,
      name: 'Nome',
      open: false,
      options: [],
      order: 1,
      priority: false,
      required: true,
      type: 'TEXT',
      weight: 0,
    },
  ],
};

const onMounted = async (props: ProcessFieldsProps) => {
  vi.mocked(ProcessFieldApi.list).mockResolvedValueOnce(processFieldMock);

  vi.mocked(analytics).mockImplementation(
    () =>
      ({
        page: vi.fn(),
      } as unknown as AnalyticsInstance)
  );

  const wrapper = mount(ProcessFields, {
    props,
    global: {
      stubs: [],
      directives: {
        tooltip: VTooltip,
      },
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('ProcessFields', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProcessFields>>;

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
    expect(wrapper.vm.responsibleFields).toMatchObject(
      processFieldMock.responsibleFields
    );
    expect(wrapper.vm.studentFields).toMatchObject(
      processFieldMock.studentFields
    );
  });

  test('if I click on "go back" button, the user should be redirected to previous page', async () => {
    const button = wrapper.find('[data-test="button-back"]');

    await button.trigger('click');

    expect(spyPush).toBeCalled();
    expect(spyPush).toBeCalledTimes(1);
    expect(spyPush).toBeCalledWith({
      name: 'process.update',
      params: {
        id: '1',
        newProcess: 'false',
      },
    });
  });

  describe('submit', () => {
    test('should submit successfully', async () => {
      vi.mocked(ProcessFieldApi.post).mockResolvedValueOnce({});

      const button = wrapper.find('[data-test="button-proceed"]');

      await button.trigger('click');

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(spyPush).toHaveBeenCalledWith({
        name: 'process.periods',
        params: {
          id: '1',
          newProcess: 'false',
        },
      });
    });

    test('should not submit successfully', async () => {
      vi.mocked(ProcessFieldApi.post).mockRejectedValueOnce({});

      const button = wrapper.find('[data-test="button-proceed"]');

      await button.trigger('click');

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(spyPush).not.toBeCalled();
    });
  });
});
