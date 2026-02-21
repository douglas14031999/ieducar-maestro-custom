import '@/plugin/sortby';
import {
  GetPreregistrations,
  PreregistrationLoad,
} from '@/modules/preregistration/types';

import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import PreRegistrationList from '@/modules/preregistration/components/PreRegistrationList.vue';
import {
  Preregistration as PreregistrationApi,
  PreregistrationRest,
} from '@/modules/preregistration/api';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import { useGeneralStore } from '@/store/general';
import { expect, vi } from 'vitest';
import fileDownload from 'js-file-download';
import { useModal } from '@/composables';

const dialogSpy = vi.fn();

vi.mock('@/composables/useModal');
vi.mock('@/modules/preregistration/api');
vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
  useRoute: () => ({}),
}));
vi.mock('js-file-download');

const spyPush = vi.fn();

const loaderMock: PreregistrationLoad = {
  errors: false,
  stats: {
    accepted: 3203,
    rejected: 1221,
    total: 4890,
    vacancies: 25518,
    waiting: 466,
  },
  preregistrations: [
    {
      grade: {
        name: '1º Ano',
      },
      id: '1',
      parent: {
        id: '123',
        type: 'REGISTRATION',
        status: 'ACCEPTED',
        position: 1,
        student: {
          name: 'Amaral',
        },
        period: {
          name: 'Vespertino',
        },
        school: {
          name: 'Escola 0001',
        },
        schoolYear: {
          year: '2021',
        },
      },
      period: { name: 'Vespertino' },
      position: 4,
      protocol: 'HLYN3T',
      school: { name: 'Escola 0001' },
      schoolYear: {
        year: '2021',
      },
      status: 'ACCEPTED',
      student: { name: 'Lívia' },
      type: 'REGISTRATION',
      waiting: {
        id: '',
        type: '',
        status: '',
        position: 1,
        student: {
          name: '',
        },
        period: {
          name: '',
        },
        school: {
          name: '',
        },
        schoolYear: {
          year: '',
        },
      },
    },
  ],
  paginator: {
    count: 25,
    currentPage: 1,
    lastPage: 196,
    perPage: 25,
    total: 4890,
  },
};

const loaderPreregistrationsMock: GetPreregistrations = {
  grades: [
    {
      id: 'string',
      key: 'string',
      label: 'string',
      name: 'string',
    },
  ],
  periods: [
    {
      id: '4',
      name: 'Integral',
    },
  ],
  processes: [
    {
      id: '2',
      key: '2',
      label: 'Educação infantil',
      name: 'Educação infantil',
      schoolYear: { year: 2021 },
      totalPreRegistrations: 2323,
    },
  ],
  schools: [
    {
      id: '999',
      key: '9999',
      label: 'BIBLIOTECA MUNICIPAL',
      name: 'BIBLIOTECA MUNICIPAL',
    },
  ],
};

const toExportMock = new Blob(
  [
    `Protocolo,"Nome do aluno(a)","Data de nascimento do(a) aluno(a)","Nome do responsável","Telefone do responsável","E-mail do responsável",Endereço,Escola,Série,Turno,Posição,"Data de inscrição","Tipo de inscrição",Situação,Justificativa,"Inicias do aluno(a)"`,
    `CHBBN6,"Mari",13/11/2017,"Najah","(99) 99999-9999",,"Rua das Orquídeas, sn, Residencial, Criciúma, CEP 99999-999","Escola 0001","Grupo IV",Integral,1,"17/11/2020 00:25:25",Matrícula,Deferido,,M.L.`,
  ],
  {
    type: 'text/csv',
  }
);

const toReportMock = new Blob(
  [
    `Protocolo,"Nome do aluno(a)","Data de nascimento do(a) aluno(a)","Nome do responsável","Telefone do responsável","E-mail do responsável",Endereço,Escola,Série,Turno,Posição,"Data de inscrição","Tipo de inscrição",Situação,Justificativa,"Inicias do aluno(a)"`,
    `CHBBN6,"Mari",13/11/2017,"Najah","(99) 99999-9999",,"Rua das Orquídeas, sn, Residencial, Criciúma, CEP 99999-999","Escola 0001","Grupo IV",Integral,1,"17/11/2020 00:25:25",Matrícula,Deferido,,M.L.`,
  ],
  {
    type: 'application/pdf',
  }
);

const fileDownloadSpy = vi.fn();

const onMounted = async (
  loader = loaderMock,
  loaderPreregistrations = loaderPreregistrationsMock
) => {
  vi.mocked(PreregistrationApi.getPreregistrations).mockResolvedValueOnce(
    loaderPreregistrations
  );
  vi.mocked(PreregistrationApi.getList).mockResolvedValueOnce(loader);

  vi.mocked(useModal).mockImplementation(() => ({
    dialog: dialogSpy,
  }));

  const wrapper = mount(PreRegistrationList, {
    global: {
      plugins: [createTestingPinia()],
      stubs: [
        'router-link',
        'router-view',
        'x-field',
        'simple-select',
        'pre-registration-batch',
      ],
      directives: {
        tooltip: VTooltip,
      },
    },
  });
  const store = useGeneralStore();
  store.auth.user = {
    level: 1,
    name: 'nome',
    schools: [],
  };
  await flushPromises();

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('PreRegistrationList', () => {
  let wrapper: VueWrapper<InstanceType<typeof PreRegistrationList>>;
  beforeEach(async () => {
    wrapper = await onMounted(loaderMock, loaderPreregistrationsMock);

    await wrapper.vm.$nextTick();
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  test('the component exists', () => {
    expect(wrapper).toBeTruthy();
  });

  test.skip('should match snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  describe('component stats', () => {
    test('the initial variables must be loaded with the correct data', () => {
      expect(wrapper.vm.stats).toMatchObject(loaderMock.stats);
    });

    test('check if stats-cards components exists', () => {
      const component = wrapper.findComponent({
        name: 'stats-cards',
      });
      expect(component).toBeTruthy();
    });
  });

  describe('load', () => {
    test('should do a successful protocol search', async () => {
      wrapper.vm.load = vi.fn();
      const btn = wrapper.find('[data-test="btn-search-pre-registration"]');
      await btn.trigger('click');
      expect(wrapper.vm.load).toBeCalledTimes(1);
      expect(wrapper.vm.load).toBeCalledWith(1);
    });

    test('should do a successful return results', async () => {
      vi.mocked(PreregistrationApi.getList).mockResolvedValueOnce(loaderMock);
      const btn = wrapper.find('[data-test="btn-search-pre-registration"]');
      await btn.trigger('click');
      await flushPromises();
      expect(wrapper.vm.stats).toMatchObject(loaderMock.stats);
      expect(wrapper.vm.preregistrations).toMatchObject(
        loaderMock.preregistrations
      );
      expect(wrapper.vm.paginator).toMatchObject(loaderMock.paginator);
      expect(wrapper.vm.checked).toMatchObject([]);
    });
  });

  describe('showPreRegistration', () => {
    test('should submit successfully', async () => {
      const button = wrapper.find('[data-test="protocol"]');
      await button.trigger('click');
      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(spyPush).toHaveBeenCalledWith({
        name: 'preregistration.modal',
        params: {
          protocol: 'HLYN3T',
        },
      });
    });
  });

  describe('Reject', () => {
    test('should click function reject', async () => {
      wrapper.vm.checked = ['1'];
      const spy = (wrapper.vm.reject = vi.fn());

      await wrapper.vm.$nextTick();
      const button = wrapper.find('[data-test="reject"]');
      await button.trigger('click');
      await wrapper.vm.$nextTick();

      expect(spy).toBeCalledTimes(1);
    });

    test('should call function reject', () => {
      wrapper.vm.reject();
      wrapper.vm.$nextTick();

      const component = wrapper.findComponent({
        name: 'pre-registration-batch',
      });
      expect(component).toBeTruthy();

      expect(wrapper.vm.step).toBe('REJECT');
      expect(wrapper.vm.showBatchModal).toBeTruthy();
    });
  });

  describe('Summon', () => {
    test('should click function summon', async () => {
      wrapper.vm.checked = ['1'];
      const spy = (wrapper.vm.summon = vi.fn());

      await wrapper.vm.$nextTick();
      const button = wrapper.find('[data-test="summon"]');
      await button.trigger('click');
      await wrapper.vm.$nextTick();

      expect(spy).toBeCalledTimes(1);
    });

    test('should call function summon', () => {
      wrapper.vm.summon();
      wrapper.vm.$nextTick();

      const component = wrapper.findComponent({
        name: 'pre-registration-batch',
      });
      expect(component).toBeTruthy();

      expect(wrapper.vm.step).toBe('SUMMON');
      expect(wrapper.vm.showBatchModal).toBeTruthy();
    });
  });

  describe('Accept', () => {
    test('should click function accept', async () => {
      wrapper.vm.filter.canAcceptInBatch = true;
      wrapper.vm.checked = ['1'];
      const spy = (wrapper.vm.accept = vi.fn());

      await wrapper.vm.$nextTick();
      const button = wrapper.find('[data-test="accept"]');
      await button.trigger('click');
      await wrapper.vm.$nextTick();

      expect(spy).toBeCalledTimes(1);
    });

    test('should call function accept', async () => {
      wrapper.vm.filter.process = '2';

      await wrapper.vm.$nextTick();
      wrapper.vm.accept();
      await wrapper.vm.$nextTick();

      expect(wrapper.vm.processYear).toBe(2021);
      expect(wrapper.vm.step).toBe('ACCEPT');
      expect(wrapper.vm.showBatchModal).toBeTruthy();
    });
  });

  describe('toExport', () => {
    test('should call function toExport', async () => {
      vi.mocked(PreregistrationRest.toExport).mockResolvedValueOnce(
        toExportMock
      );
      vi.mocked(fileDownload).mockImplementationOnce(
        fileDownloadSpy(toExportMock, 'pre-matricula.csv')
      );

      wrapper.vm.toExport();

      await wrapper.vm.$nextTick();

      expect(fileDownloadSpy).toBeCalledTimes(1);
      expect(fileDownloadSpy).toBeCalledWith(toExportMock, 'pre-matricula.csv');
    });

    test('should not call function toExport', async () => {
      vi.mocked(PreregistrationRest.toExport).mockRejectedValueOnce(
        toExportMock
      );
      await wrapper.vm.$nextTick();
      wrapper.vm.toExport();
      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(fileDownloadSpy).not.toHaveBeenCalled();
    });

    test('should click function toExport', async () => {
      const spyToExport = vi.spyOn(wrapper.vm.ctx, 'toExport');

      vi.mocked(PreregistrationRest.toExport).mockResolvedValueOnce(
        toExportMock
      );

      const button = wrapper.find('[data-test="toExport"]');

      await button.trigger('click');

      await wrapper.vm.$nextTick();

      expect(spyToExport).toBeCalledTimes(1);
    });
  });

  describe('toReport', () => {
    test('should call function toReport', async () => {
      vi.mocked(PreregistrationRest.toReport).mockResolvedValueOnce(
        toReportMock
      );
      vi.mocked(fileDownload).mockImplementationOnce(fileDownloadSpy());

      wrapper.vm.filter = {
        canAcceptInBatch: false,
        grade: null,
        period: null,
        process: '2',
        school: null,
        schools: null,
        search: '',
        showStudentShortName: true,
        sort: null,
        status: null,
        template: 2,
        type: null,
        year: 2021,
      };

      wrapper.vm.toReport();

      await flushPromises();
      await wrapper.vm.$nextTick();

      expect(fileDownloadSpy).toBeCalledTimes(1);
      expect(wrapper.vm.showModalReportOptions).toBeFalsy();
    });

    test('should not call function toReport', async () => {
      vi.mocked(PreregistrationRest.toReport).mockRejectedValueOnce(
        toReportMock
      );

      wrapper.vm.filter = {
        canAcceptInBatch: false,
        grade: null,
        period: null,
        process: '2',
        school: null,
        schools: null,
        search: '',
        showStudentShortName: true,
        sort: null,
        status: null,
        template: 2,
        type: null,
        year: 2021,
      };

      await wrapper.vm.$nextTick();
      wrapper.vm.toReport();
      await wrapper.vm.$nextTick();
      await flushPromises();

      expect(dialogSpy).toHaveBeenCalledWith({
        title: 'Atenção!',
        description:
          'Não foi possível emitir o relatório. Verifique os filtros e tente novamente.',
        iconLeft: 'status-red',
        titleClass: 'danger',
      });
      expect(fileDownloadSpy).not.toHaveBeenCalled();
    });
  });
});
