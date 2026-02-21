import '@/plugin/sortby';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Protocol as ProtocolApi } from '@/modules/protocol/api';
import ProtocolFind from '@/modules/protocol/components/ProtocolFind.vue';
import VTooltip from 'v-tooltip';
import filters from '@/filters';
import { expect, vi } from 'vitest';
import { useModal } from '@/composables';

const dialogSpy = vi.fn();

vi.mock('@/composables/useModal');
vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));
vi.mock('@/modules/protocol/api');

const spyPush = vi.fn();

const studentProtocolMock = [
  {
    id: 1,
    protocol: 'string',
    status: 'string',
    process: {
      schoolYear: {
        year: 2021,
      },
    },
    student: {
      initials: 'string',
      dateOfBirth: 'string',
    },
  },
];

const onMounted = async () => {
  vi.mocked(useModal).mockImplementation(() => ({
    dialog: dialogSpy,
  }));

  const wrapper = mount(ProtocolFind, {
    props: {
      type: 'CPF',
      student: {
        student_cpf: '000.000.000-00',
      },
    },
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

describe('ProtocolFind', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProtocolFind>>;

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

  test('check if the initial props is correct', () => {
    expect(wrapper.vm.type).toBe('CPF');
    expect(wrapper.vm.student).toEqual({
      student_cpf: '000.000.000-00',
    });
  });

  test('check if I have 1 x-form and 1 modal', () => {
    const xform = wrapper.findComponent({
      name: 'x-form',
    });

    expect(xform).toBeTruthy();

    const modals = wrapper.findAllComponents({
      name: 'modal',
    });

    expect(modals).toBeTruthy();
    expect(modals.length).toBe(1);
  });

  test('should trigger "Prosseguir" button, and successfully make a request', async () => {
    vi.mocked(ProtocolApi.postFindProtocol).mockResolvedValueOnce(
      studentProtocolMock
    );

    const button = wrapper.findAll('[type="button"]').at(1);

    await button?.trigger('click');

    wrapper.vm.submit();

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(wrapper.vm.open).toBe(true);
  });

  test('should trigger "Prosseguir" button, and unsuccessfully make a request', async () => {
    vi.mocked(ProtocolApi.postFindProtocol).mockResolvedValueOnce([]);

    const button = wrapper.findAll('[type="button"]').at(1);

    await button?.trigger('click');

    wrapper.vm.submit();

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(dialogSpy).toHaveBeenCalledWith({
      title: 'Nenhum protocolo encontrado',
      titleClass: 'danger',
      iconLeft: 'status-red',
      // this line must be with this identation (4 tabs)
      description: `Verifique se os dados informados estão corretos. Caso não encontre nenhum resultado, clique em 'Voltar' e
          efetue a validação com outro tipo de dado, ou efetue uma pré-matrícula para o(a) aluno(a)`,
    });
  });

  test('should trigger "close" function, and open must be false', async () => {
    wrapper.vm.open = true;

    expect(wrapper.vm.open).toBeTruthy();

    wrapper.vm.close();

    await wrapper.vm.$nextTick();

    expect(wrapper.vm.open).toBeFalsy();
  });

  test('should trigger "prev" function, route must be pushed to "/onde-encontro-o-protocolo"', async () => {
    wrapper.vm.prev();

    await wrapper.vm.$nextTick();

    expect(spyPush).toHaveBeenCalled();
    expect(spyPush).toHaveBeenCalledTimes(1);
    expect(spyPush).toHaveBeenCalledWith('/onde-encontro-o-protocolo');
  });

  test('should trigger "next" function, submit function must be called', async () => {
    const spy = vi.spyOn(wrapper.vm.ctx, 'submit');

    spy.mockImplementationOnce(() => ({}));

    wrapper.vm.next();

    await wrapper.vm.$nextTick();

    expect(spy).toHaveBeenCalled();
    expect(spy).toHaveBeenCalledTimes(1);
  });

  test('when submit function is called, correct studentModel must be filled', async () => {
    const preregistrationsMock = [
      {
        id: 1,
        protocol: 'string',
        status: 'string',
        process: {
          schoolYear: {
            year: 2022,
          },
        },
        student: {
          initials: 'string',
          dateOfBirth: 'string',
        },
      },
      {
        id: 2,
        protocol: 'string',
        status: 'string',
        process: {
          schoolYear: {
            year: 2021,
          },
        },
        student: {
          initials: 'string',
          dateOfBirth: 'string',
        },
      },
      {
        id: 3,
        protocol: 'string',
        status: 'string',
        process: {
          schoolYear: {
            year: 2023,
          },
        },
        student: {
          initials: 'string',
          dateOfBirth: 'string',
        },
      },
    ];

    vi.mocked(ProtocolApi.postFindProtocol).mockResolvedValueOnce(
      preregistrationsMock
    );

    wrapper.vm.submit();

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(wrapper.vm.studentModel.preregistrations).toStrictEqual(
      preregistrationsMock
    );
  });
});
