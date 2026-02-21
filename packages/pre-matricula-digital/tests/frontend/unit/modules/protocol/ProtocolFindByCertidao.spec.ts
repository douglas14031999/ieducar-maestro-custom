import { VueWrapper, mount } from '@vue/test-utils';
import ProtocolFindByCertidao from '@/modules/protocol/components/ProtocolFindByCertidao.vue';
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
  const wrapper = mount(ProtocolFindByCertidao, {
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

describe('ProtocolFindByCertidao', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProtocolFindByCertidao>>;

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

  test('should fill "Certidão de nascimento do(a) aluno(a)" input successfully', async () => {
    const input = wrapper.findComponent({
      name: 'x-field-stub',
    });

    expect(input).toBeTruthy();

    wrapper.vm.student.student_birth_certificate =
      '12312321123123123213123213123212';

    expect(wrapper.vm.student.student_birth_certificate).toBe(
      '12312321123123123213123213123212'
    );
  });
});
