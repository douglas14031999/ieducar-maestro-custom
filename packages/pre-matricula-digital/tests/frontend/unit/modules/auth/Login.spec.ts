import { VueWrapper, flushPromises, shallowMount } from '@vue/test-utils';
import { Auth } from '@/modules/auth/types';
import { AuthRest } from '@/modules/auth/api';
import Login from '@/modules/auth/components/Login.vue';
import { expect, vi } from 'vitest';

vi.mock('@/modules/auth/api');

const spyPush = vi.fn();

vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));

const loginMock: Auth = {
  level: 1,
  name: 'ESCOLA ESTADUAL DE CIENCIAS DA SAÚDE',
  schools: [],
};

const errorMessage = `
      O login de acesso no <strong>Pré-matrícula Digital</strong> é exclusivo para secretários e diretores escolares,
      ou gestores da Secretaria de Educação.
      <br>
      No momento, para acessar o sistema você deve estar logado no i-Educar.
`;

const onMounted = async (success: boolean) => {
  if (success) {
    vi.mocked(AuthRest.getAuth).mockResolvedValueOnce(loginMock);
  } else {
    vi.mocked(AuthRest.getAuth).mockRejectedValueOnce(null);
  }

  const wrapper = shallowMount(Login);

  await flushPromises();

  return wrapper;
};

describe('Login', () => {
  let wrapper: VueWrapper<InstanceType<typeof Login>>;

  beforeEach(async () => {
    wrapper = await onMounted(true);

    await wrapper.vm.$nextTick();
  });

  afterEach(() => {
    vi.clearAllMocks();
    vi.resetAllMocks();
  });

  test('the component exists', async () => {
    expect(wrapper).toBeTruthy();
  });

  test('should match snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  test('login has made successfully', async () => {
    expect(spyPush).toHaveBeenCalledTimes(1);
    expect(spyPush).toHaveBeenCalledWith({ name: 'preregistrations' });
  });

  test('displays error message if success is false', async () => {
    wrapper = await onMounted(false);

    await wrapper.vm.$nextTick();

    expect(spyPush).toHaveBeenCalledTimes(1);
    expect(wrapper.vm.message).toContain(errorMessage);
  });
});
