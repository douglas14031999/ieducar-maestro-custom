import { VueWrapper, mount } from '@vue/test-utils';
import ProtocolFindByNomeDataNascimento from '@/modules/protocol/components/ProtocolFindByNomeDataNascimento.vue';
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
  const wrapper = mount(ProtocolFindByNomeDataNascimento, {
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

describe('ProtocolFindByNomeDataNascimento', () => {
  let wrapper: VueWrapper<
    InstanceType<typeof ProtocolFindByNomeDataNascimento>
  >;

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

  test('should fill "NOME DO(A) ALUNO(A)" and "DATA DE NASCIMENTO DO(A) ALUNO(A)" input successfully', async () => {
    const input = wrapper.findComponent({
      name: 'x-field-stub',
    });

    expect(input).toBeTruthy();

    wrapper.vm.student.student_name = 'Nome do aluno';

    expect(wrapper.vm.student.student_name).toBe('Nome do aluno');

    wrapper.vm.student.student_date_of_birth = '20/06/1997';

    expect(wrapper.vm.student.student_date_of_birth).toBe('20/06/1997');
  });
});
