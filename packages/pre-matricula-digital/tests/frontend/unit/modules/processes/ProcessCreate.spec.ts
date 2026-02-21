import {
  ProcessCreate as IProcessCreate,
  ProcessCreateListCourses,
} from '@/modules/processes/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Process as ProcessApi } from '@/modules/processes/api';
import ProcessCreate from '@/modules/processes/components/ProcessCreate.vue';
import { expect, vi } from 'vitest';
import { analytics } from '@/packages';
import { AnalyticsInstance } from 'analytics';

vi.mock('@/modules/processes/api');
vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));
vi.mock('@/packages');

const spyPush = vi.fn();

interface ProcessCreateProps {
  id?: string;
  newProcess: string;
}

const processMock: IProcessCreate = {
  id: '1',
  name: 'Processo de testes',
  active: true,
  forceSuggestedGrade: false,
  gradeAgeRangeLink: null,
  grades: ['36', '14'],
  messageFooter: '',
  periods: ['1', '2'],
  schoolYear: '2022',
  showPriorityProtocol: true,
  allowResponsibleSelectMapAddress: false,
};

const processCreateListCoursesMock: ProcessCreateListCourses = {
  courses: [
    {
      id: '300',
      name: 'ENSINO FUNDAMENTAL - 1º A 5º',
      grades: [
        { id: '36', name: '4º Ano' },
        { id: '6', name: '3º Ano' },
        { id: '14', name: '5º Ano' },
        { id: '22', name: '1º Ano' },
        { id: '40', name: '2º Ano' },
      ],
    },
  ],
  periods: [
    { id: '1', name: 'Matutino' },
    { id: '2', name: 'Vespertino' },
    { id: '3', name: 'Noturno' },
    { id: '4', name: 'Integral' },
  ],
  schoolYears: [
    { key: '2020', label: '2020' },
    { key: '2021', label: '2021' },
    { key: '2022', label: '2022' },
  ],
};

const onMounted = async (
  props: ProcessCreateProps,
  listCourses?: ProcessCreateListCourses
) => {
  if (listCourses) {
    vi.mocked(ProcessApi.listCourses).mockResolvedValueOnce(listCourses);
  } else {
    vi.mocked(ProcessApi.listCourses).mockRejectedValueOnce({});
  }

  vi.mocked(analytics).mockImplementation(
    () =>
      ({
        page: vi.fn(),
      } as unknown as AnalyticsInstance)
  );

  const wrapper = mount(ProcessCreate, {
    props,
    global: {
      stubs: ['router-link', 'x-field', 'Field', 'ErrorMessage'],
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('ProcessCreate', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProcessCreate>>;

  beforeEach(async () => {
    wrapper = await onMounted(
      {
        newProcess: 'true',
      },
      processCreateListCoursesMock
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
    expect(wrapper.vm.courses).toMatchObject(
      processCreateListCoursesMock.courses
    );
    expect(wrapper.vm.periods).toMatchObject(
      processCreateListCoursesMock.periods
    );
    expect(wrapper.vm.schoolYears).toMatchObject(
      processCreateListCoursesMock.schoolYears
    );
  });

  test('when I click in "go back" button, the router should be redirected to previous page', async () => {
    const button = wrapper.find('[data-test="back-button"]');

    await button.trigger('click');

    await wrapper.vm.$nextTick();

    expect(spyPush).toBeCalledTimes(1);
    expect(spyPush).toBeCalledWith({
      name: 'process.show',
    });
  });

  test('should not populate initial variables if occurs an error on load courses', async () => {
    wrapper = await onMounted({
      newProcess: 'true',
    });

    await wrapper.vm.$nextTick();

    expect(wrapper.vm.courses).toMatchObject([]);
    expect(wrapper.vm.periods).toMatchObject([]);
    expect(wrapper.vm.schoolYears).toMatchObject([]);
  });

  describe('submit', () => {
    test('when I click in "submit" button, the form should be submitted', async () => {
      vi.mocked(ProcessApi.post).mockResolvedValueOnce({
        id: '1',
      });

      wrapper.vm.submit({
        ...processMock,
        id: null,
      });

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(spyPush).toBeCalledWith({
        name: 'process.fields',
        params: {
          id: '1',
          newProcess: 'true',
        },
      });
    });

    test('when I click in "submit" button with model error, the form should not be submitted', async () => {
      vi.mocked(ProcessApi.post).mockRejectedValueOnce({});

      wrapper.vm.submit({
        id: '1',
      });

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(spyPush).not.toBeCalled();
    });
  });

  describe('/processos/:id/editar', () => {
    beforeEach(async () => {
      vi.mocked(ProcessApi.listCreate).mockResolvedValueOnce(processMock);

      wrapper = await onMounted(
        {
          newProcess: 'false',
          id: '1',
        },
        processCreateListCoursesMock
      );

      await wrapper.vm.$nextTick();
    });

    afterEach(() => {
      vi.clearAllMocks();
    });

    test('the initial variables must be loaded with the correct data', () => {
      expect(wrapper.vm.courses).toMatchObject(
        processCreateListCoursesMock.courses
      );
      expect(wrapper.vm.periods).toMatchObject(
        processCreateListCoursesMock.periods
      );
      expect(wrapper.vm.schoolYears).toMatchObject(
        processCreateListCoursesMock.schoolYears
      );
      expect(wrapper.vm.process).toMatchObject(processMock);
    });
  });
});
