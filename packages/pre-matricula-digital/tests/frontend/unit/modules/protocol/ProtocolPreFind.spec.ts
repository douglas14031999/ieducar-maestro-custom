import { VueWrapper, mount } from '@vue/test-utils';
import ProtocolPreFind from '@/modules/protocol/components/ProtocolPreFind.vue';
import { expect, vi } from 'vitest';

vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));

const spyPush = vi.fn();

const onMounted = async () => {
  const wrapper = mount(ProtocolPreFind, {
    global: {
      plugins: [],
      stubs: [],
    },
  });

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('ProtocolPreFind', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProtocolPreFind>>;
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

  test('check if exists 5 buttons', () => {
    const buttons = wrapper.findAll('button');

    expect(buttons.length).toBe(4);
  });

  test('when I click in "CPF do(a) aluno(a)" button, it should go to "/busca-protocolo-cpf"', async () => {
    const btn = wrapper.find('[data-test="btn-1"]');

    await btn.trigger('click');

    await wrapper.vm.$nextTick();

    expect(spyPush).toHaveBeenCalledTimes(1);
    expect(spyPush).toHaveBeenCalledWith('/busca-protocolo-cpf');
  });

  test('when I click in "RG do(a) aluno(a)" button, it should go to "/busca-protocolo-rg"', async () => {
    const btn = wrapper.find('[data-test="btn-2"]');

    await btn.trigger('click');

    await wrapper.vm.$nextTick();

    expect(spyPush).toHaveBeenCalledTimes(1);
    expect(spyPush).toHaveBeenCalledWith('/busca-protocolo-rg');
  });

  test('when I click in "Certidão de nascimento do(a) aluno(a)" button, it should go to "/busca-protocolo-certidao"', async () => {
    const btn = wrapper.find('[data-test="btn-3"]');

    await btn.trigger('click');

    await wrapper.vm.$nextTick();

    expect(spyPush).toHaveBeenCalledTimes(1);
    expect(spyPush).toHaveBeenCalledWith('/busca-protocolo-certidao');
  });

  test('when I click in "Nome e data de nascimento do(a) aluno(a)" button, it should go to "/busca-protocolo-nome-e-nascimento"', async () => {
    const btn = wrapper.find('[data-test="btn-4"]');

    await btn.trigger('click');

    await wrapper.vm.$nextTick();

    expect(spyPush).toHaveBeenCalledTimes(1);
    expect(spyPush).toHaveBeenCalledWith('/busca-protocolo-nome-e-nascimento');
  });
});
