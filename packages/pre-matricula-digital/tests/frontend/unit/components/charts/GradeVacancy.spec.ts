import { VueWrapper, mount } from '@vue/test-utils';
import GradeVacancy from '@/components/charts/GradeVacancy.vue';
import VTooltip from 'v-tooltip';
import { expect, vi } from 'vitest';

const mockData = {
  id: '1',
  name: 'TESTE',
  total: 1,
  available: 1,
  waiting: 1,
  accepted: 1,
  rejected: 1,
  exceded: false,
};

const mockGrade = '1º Ano';

const onMounted = async () => {
  const wrapper = mount(GradeVacancy, {
    global: {
      directives: {
        tooltip: VTooltip,
      },
    },
    props: {
      data: mockData,
      grade: mockGrade,
    },
  });

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('GradeVacancy', () => {
  let wrapper: VueWrapper<InstanceType<typeof GradeVacancy>>;

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

  test('the component has the correct data', () => {
    expect(wrapper.vm.data).toMatchObject(mockData);
    expect(wrapper.vm.grade).toBe(mockGrade);

    expect(wrapper.vm.preregistrations).toBe(4);
    expect(wrapper.vm.opened).toBe(25);
    expect(wrapper.vm.accepted).toBe(25);
    expect(wrapper.vm.rejected).toBe(25);
    expect(wrapper.vm.waiting).toBe(25);
  });

  test('when vacancy is exceded, should return message about vacancy exceded', async () => {
    wrapper.vm.data.exceded = true;
    await wrapper.vm.$nextTick();

    const element = wrapper.find('[data-test="data-exceded"]');
    expect(element.exists()).toBeTruthy();
  });

  test('when vacancy is not exceded, should not return message about vacancy exceded', async () => {
    wrapper.vm.data.exceded = false;
    await wrapper.vm.$nextTick();

    const element = wrapper.find('[data-test="data-exceded"]');
    expect(element.exists()).toBeFalsy();
  });
});
