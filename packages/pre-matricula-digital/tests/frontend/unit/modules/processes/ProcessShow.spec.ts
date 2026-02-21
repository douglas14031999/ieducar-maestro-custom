import '@/plugin/sortby';
import type {
  ProcessPostAction,
  ProcessShow as ProcessShowType,
} from '@/modules/processes/types';
import { RouteLocationNormalizedLoaded, useRoute, useRouter } from 'vue-router';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Process as ProcessApi } from '@/modules/processes/api';
import ProcessShow from '@/modules/processes/components/ProcessShow.vue';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import filters from '@/filters';
import { useGeneralStore } from '@/store/general';
import { expect, vi } from 'vitest';
import { analytics } from '@/packages';
import { AnalyticsInstance } from 'analytics';
import { useModal } from '@/composables';

const dialogSpy = vi.fn();

vi.mock('@/composables/useModal');
vi.mock('@/modules/processes/api');
vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
  useRoute: () => ({
    params: {
      id: processMock.id,
    },
  }),
}));
vi.mock('@/packages');

const spyPush = vi.fn();

const processMock: ProcessShowType = {
  fields: [
    {
      field: {
        name: 'Nome do(a) responsável',
        group: 'RESPONSIBLE',
        type: 'TEXT',
      },
      id: '184',
      required: true,
      weight: 0,
    },
    {
      field: {
        name: 'Nome',
        group: 'STUDENT',
        type: 'TEXT',
      },
      id: '197',
      required: true,
      weight: 0,
    },
  ],
  id: '9',
  name: 'Ed. Infantil (Nível III, Pré-Escola) Ens. Fundamental (1° ao 9° ano)',
  periods: [
    { id: '1', name: 'Matutino' },
    { id: '2', name: 'Vespertino' },
    { id: '4', name: 'Integral' },
  ],
  schoolYear: {
    year: '2021',
  },
  stages: [
    {
      endAt: '2021-03-07 23:59:00',
      id: '17',
      name: 'Test',
      observation:
        '<p>Lista de documentos necess&aacute;rios do aluno:</p><p><br></p><ul><li>a) Certid&atilde;o de nascimento ou documento oficial com foto;</li><li>b) Comprovante de Cadastro de Pessoa F&iacute;sica - CPF, da crian&ccedil;a pleiteante &agrave; vaga;</li></ul><p>Lista de documentos necess&aacute;rios dos respons&aacute;veis, caso convivam maritalmente:</p><p><br></p><ul><li>a) Certid&atilde;o de casamento ou de uni&atilde;o est&aacute;vel;</li><li>b) Documento de identifica&ccedil;&atilde;o de ambos com foto (RG ou CNH ou Identidade profissional emitida por org&atilde;o oficial);</li><li>c)Comprova&ccedil;&atilde;o de exerc&iacute;cio de atividade remunerada;</li></ul><p>Lista de documentos necess&aacute;rios dos respons&aacute;veis, caso sejam separados:</p><p><br></p><ul><li>a) Documento ou declara&ccedil;&atilde;o que comprobe a situa&ccedil;&atilde;o do casal (divorciado, separado judicialmente, etc);</li><li>b) Termo de guarda da crian&ccedil;a em favor do respons&aacute;vel pela matr&iacute;cula;</li><li>c)Comprova&ccedil;&atilde;o de exerc&iacute;cio de atividade remunerada;</li></ul><p>Lista geral de documentos necess&aacute;rios para efetivar a matr&iacute;cula do aluno:</p><p><br></p><ul><li>a) Comprovante de resid&ecirc;ncia (fatura de &aacute;gua, energia, telefone fixo, etc);</li><li>b) Declara&ccedil;&atilde;o de vacina&ccedil;&atilde;o atualizada.;</li><li>c) C&oacute;pia de certid&atilde;o de nascimento de irm&atilde;os menores de 14 anos.;</li><li>d) C&oacute;pia do atestado m&eacute;dico das restri&ccedil;&otilde;es de sa&uacute;de, laudous m&eacute;dicos, alimentar, se houver.;</li><li>e) C&oacute;pia do Hist&oacute;rico Escolar ou declara&ccedil;&atilde;o de frequ&ecirc;ncia (para alunos transferidos);</li><li>f) Protocolo de visto de perman&ecirc;ncia para familias estrangeiras;</li><li>g) Cart&atilde;o do Bolsa Fam&iacute;lia (quando benefici&aacute;rio);</li></ul><p>Datas de comparecimento na unidade para entrega da documenta&ccedil;&atilde;o:</p><p><br></p><p>EM CASO DE DEFERIDO - COMPARECER NA ESCOLA AT&Eacute; DIA 22 DE MAR&Ccedil;O DE 2021 PARA EFETUAR A MATR&Iacute;CULA.</p>',
      startAt: '2021-02-17 14:00:00',
      status: 'CLOSED',
      totalWaitingPreRegistrations: 1,
      type: 'REGISTRATION',
    },
  ],
};

const onMounted = async (process: ProcessShowType, error = false) => {
  if (!error) {
    vi.mocked(ProcessApi.getShow).mockResolvedValueOnce(process);
  } else {
    vi.mocked(ProcessApi.getShow).mockRejectedValueOnce(process);
  }

  vi.mocked(analytics).mockImplementation(
    () =>
      ({
        page: vi.fn(),
      } as unknown as AnalyticsInstance)
  );

  vi.mocked(useModal).mockImplementation(() => ({
    dialog: dialogSpy,
  }));

  const wrapper = mount(ProcessShow, {
    global: {
      plugins: [createTestingPinia()],
      provide: {
        $filters: filters,
      },
      stubs: ['x-field'],
      directives: {
        tooltip: VTooltip,
      },
    },
  });

  const store = useGeneralStore();

  store.auth.user = {
    name: 'TEST USER',
    level: 1,
    schools: [],
  };

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('ProcessShow', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProcessShow>>;

  beforeEach(async () => {
    wrapper = await onMounted(processMock);

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
    expect(wrapper.vm.process).toMatchObject(processMock);
    expect(wrapper.vm.process.stages.length).toEqual(1);
    expect(wrapper.vm.process.stages[0].totalWaitingPreRegistrations).toEqual(
      1
    );
  });

  test('should redirect to edit page when the edit button is clicked', async () => {
    const editButton = wrapper.find('[data-test="btn-edit"]');

    await editButton.trigger('click');

    await wrapper.vm.$nextTick();

    expect(spyPush).toBeCalledWith({
      name: 'process.update',
      params: { id: '9' },
    });
  });

  describe('deleteProcess', () => {
    test('should set showConfirmDeleteProcess to true', async () => {
      const deleteBtn = wrapper.find('[data-test="btn-delete"]');

      await deleteBtn.trigger('click');

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.showConfirmDeleteProcess).toBeTruthy();
    });

    test('should call the deleteProcess method', async () => {
      wrapper.vm.deleteProcess = vi.fn();

      wrapper.vm.deleteProcess();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.deleteProcess).toBeCalledTimes(1);
    });

    test('should call the deleteProcess method with the correct params', async () => {
      vi.mocked(ProcessApi.remove).mockResolvedValueOnce(
        {} as ProcessPostAction
      );

      wrapper.vm.processNameToDelete =
        'Ed. Infantil (Nível III, Pré-Escola) Ens. Fundamental (1° ao 9° ano)';

      wrapper.vm.deleteProcess();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(wrapper.vm.errorDeleteProcess).toBeFalsy();

      expect(spyPush).toBeCalledTimes(1);
      expect(spyPush).toBeCalledWith({ name: 'processes' });

      expect(wrapper.vm.showConfirmDeleteProcess).toBeFalsy();
    });

    test('should call the deleteProcess method and throw errors', async () => {
      vi.mocked(ProcessApi.remove).mockRejectedValueOnce(
        {} as ProcessPostAction
      );

      wrapper.vm.processNameToDelete =
        'Ed. Infantil (Nível III, Pré-Escola) Ens. Fundamental (1° ao 9° ano)';

      wrapper.vm.deleteProcess();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(wrapper.vm.errorDeleteProcess).toBeFalsy();

      expect(spyPush).toBeCalledTimes(0);

      expect(dialogSpy).toHaveBeenCalledWith({
        title: 'Erro!',
        description:
          'Não foi possível remover o processo. Por favor, verifique se existem inscrições neste processo.',
        iconLeft: 'status-red',
        titleClass: 'danger',
      });

      expect(wrapper.vm.showConfirmDeleteProcess).toBeFalsy();
    });

    test('process and processNameToDelete should be different and the method must return', async () => {
      wrapper.vm.processNameToDelete = 'Some different name';

      wrapper.vm.deleteProcess();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.errorDeleteProcess).toBeTruthy();
    });
  });

  describe('copyProcess', () => {
    test('should set showConfirmCopy to true', async () => {
      const deleteBtn = wrapper.find('[data-test="btn-copy"]');

      await deleteBtn.trigger('click');

      await wrapper.vm.$nextTick();

      expect(dialogSpy).toBeCalledTimes(1);
    });

    test('should call the copyProcess method', async () => {
      wrapper.vm.copyProcess = vi.fn();

      wrapper.vm.copyProcess();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.copyProcess).toBeCalledTimes(1);
    });

    test('should call the copyProcess method with the correct params', async () => {
      vi.mocked(ProcessApi.postCopy).mockResolvedValueOnce({
        process: {
          id: '1',
        },
      } as ProcessPostAction);

      wrapper.vm.copyProcess();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(spyPush).toBeCalledTimes(1);
      expect(spyPush).toBeCalledWith({
        name: 'process.update',
        params: { id: '1' },
      });
    });

    test('should call the deleteProcess method and throw errors', async () => {
      vi.mocked(ProcessApi.postCopy).mockRejectedValueOnce(
        {} as ProcessPostAction
      );

      wrapper.vm.copyProcess();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(spyPush).toBeCalledTimes(0);

      expect(dialogSpy).toHaveBeenCalledWith({
        title: 'Erro',
        description:
          'Não foi possível copiar o processo. Por favor, entre em contato com o suporte.',
        titleClass: 'danger',
        iconLeft: 'status-red',
      });
    });
  });

  describe('rejectInBatch', () => {
    test('should set showModalConfirmrejectInBatch to true', async () => {
      const btn = wrapper.find('[data-test="btn-reject-in-batch"]');

      await btn.trigger('click');

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.stageToRejectInBatch).toMatchObject(
        processMock.stages[0]
      );
      expect(wrapper.vm.totalWaitingPreRegistrations).toBe(1);
      expect(wrapper.vm.showModalConfirmrejectInBatch).toBeTruthy();
    });

    test('should call the rejectInBatch method', async () => {
      wrapper.vm.rejectInBatch = vi.fn();

      wrapper.vm.rejectInBatch();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.rejectInBatch).toBeCalledTimes(1);
    });

    test('should call the rejectInBatch method with the correct params', async () => {
      vi.mocked(ProcessApi.getShow).mockResolvedValueOnce(processMock);

      vi.mocked(ProcessApi.postRejectInBatch).mockResolvedValueOnce({
        rejectInBatch: 1,
      });

      wrapper.vm.rejectInBatch();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(dialogSpy).toHaveBeenCalledWith({
        title: 'Sucesso!',
        titleClass: 'success',
        description: '1 pré-matricula(s) indeferida(s) com sucesso!',
      });
      expect(wrapper.vm.showModalConfirmrejectInBatch).toBe(false);
      expect(wrapper.vm.justification).toBe('');
    });

    test('should call the rejectInBatch method and throw errors', async () => {
      vi.mocked(ProcessApi.getShow).mockResolvedValueOnce(processMock);

      vi.mocked(ProcessApi.postRejectInBatch).mockRejectedValueOnce({});

      wrapper.vm.rejectInBatch();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(dialogSpy).toHaveBeenCalledWith({
        title: 'Erro!',
        description:
          'Não foi possível indeferir matriculas em lote. Por favor, entre em contato com o suporte.',
        iconLeft: 'status-red',
        titleClass: 'danger',
      });
    });
  });
});
