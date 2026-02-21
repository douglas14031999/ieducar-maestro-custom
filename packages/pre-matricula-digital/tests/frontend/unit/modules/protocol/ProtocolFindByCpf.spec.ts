import { VueWrapper, mount } from '@vue/test-utils';
import ProtocolFindByCpf from '@/modules/protocol/components/ProtocolFindByCpf.vue';
import VTooltip from 'v-tooltip';
import filters from '@/filters';
import { expect, vi } from 'vitest';

vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));

const spyPush = vi.fn();

const onMounted = async () => {
  const wrapper = mount(ProtocolFindByCpf, {
    global: {
      stubs: ['x-field', 'router-link'],
      directives: {
        tooltip: VTooltip,
      },
      mocks: {
        $router: () => ({
          push: spyPush,
        }),
      },
      provide: {
        $filters: filters,
      },
    },
  });

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('ProtocolFindByCpf', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProtocolFindByCpf>>;

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

  test('should fill "CPF DO(A) ALUNO(A)" input successfully', async () => {
    const input = wrapper.findComponent({
      name: 'x-field-stub',
    });

    expect(input).toBeTruthy();

    wrapper.vm.student.student_cpf = '000.000.000-00';

    expect(wrapper.vm.student.student_cpf).toBe('000.000.000-00');
  });
});
