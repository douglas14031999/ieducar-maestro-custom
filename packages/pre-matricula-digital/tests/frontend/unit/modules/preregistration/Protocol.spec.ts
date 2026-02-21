import {
  PreRegistrationOverview,
  PreRegistrationResponsibleField,
  PreRegistrationStageProcess,
  PreRegistrationStudentField,
} from '@/modules/preregistration/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Preregistration as PreregistrationApi } from '@/modules/preregistration/api';
import Protocol from '@/modules/preregistration/components/Protocol.vue';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import filters from '@/filters';
import { expect, vi } from 'vitest';

vi.mock('@/modules/preregistration/api');

interface ProtocolProps {
  preregistrations: PreRegistrationOverview[];
  process: PreRegistrationStageProcess;
  student: PreRegistrationStudentField;
  responsible: PreRegistrationResponsibleField;
}

const propsMock: ProtocolProps = {
  preregistrations: [],
  responsible: {
    relationType: null,
    address: {
      postalCode: '',
      address: '',
      number: '',
      complement: '',
      neighborhood: '',
      lat: 0,
      lng: 0,
      city: '',
      cityIbgeCode: 0,
      stateAbbreviation: '',
    },
    useSecondAddress: false,
    secondAddress: {
      postalCode: '',
      address: '',
      number: '',
      complement: '',
      neighborhood: '',
      lat: 0,
      lng: 0,
      city: '',
      cityIbgeCode: 0,
      stateAbbreviation: '',
    },
    responsible_name: null,
  } as PreRegistrationResponsibleField,
  student: {
    grade: null,
    period: null,
    school: null,
    useSecondSchool: false,
    secondSchool: null,
    secondPeriod: null,
    match: null,
    student_name: null,
    student_date_of_birth: '2015-01-01T00:00:00.000Z',
    student_cpf: 'string',
    student_rg: 'string',
    student_marital_status: 1,
    student_place_of_birth: 1,
    student_birth_certificate: 'string',
    student_gender: 1,
    student_email: 'string',
    student_phone: 'string',
    student_mobile: 'string',
  },
  process: {
    forceSuggestedGrade: true,
    gradeAgeRangeLink: null,
    id: '1',
    messageFooter:
      'Acompanhe a publicação do resultado classificatório e convocação no site da Prefeitura.',
    name: 'PROCESSO DE TESTES 2022',
    showPriorityProtocol: true,
    grades: [
      {
        endBirth: '2016-03-31',
        id: '22',
        name: '1º Ano',
        startBirth: '2015-03-31',
      },
    ],
    periods: [
      {
        id: '1',
        name: 'Matutino',
      },
    ],
    schoolYear: { year: 2022 },
    schools: [
      {
        id: '12345',
        latitude: 0,
        longitude: 0,
        name: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
      },
    ],
    vacancies: [
      {
        available: 8,
        grade: 6,
        period: 1,
        school: 12345,
        total: 25,
      },
    ],
    fields: [
      {
        field: {
          group: 'RESPONSIBLE',
          id: '1',
          internal: 'responsible_name',
          name: 'Nome do(a) responsável',
          options: [],
          type: 'TEXT',
        },
        id: '262',
        order: '1',
        required: true,
        weight: 0,
      },
      {
        field: {
          group: 'STUDENT',
          id: '9',
          internal: 'student_name',
          name: 'Nome',
          options: [],
          type: 'TEXT',
        },
        id: '267',
        order: '2',
        required: true,
        weight: 0,
      },
    ],
  },
};

const onMounted = async (props: ProtocolProps) => {
  const wrapper = mount(Protocol, {
    props,
    global: {
      plugins: [createTestingPinia()],
      stubs: ['x-field'],
      directives: {
        tooltip: VTooltip,
      },
      provide: {
        $filters: filters,
      },
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('Protocol', () => {
  let wrapper: VueWrapper<InstanceType<typeof Protocol>>;

  beforeEach(async () => {
    wrapper = await onMounted(propsMock);

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

  test('should call window.print', () => {
    const spy = (window.print = vi.fn());

    wrapper.vm.print();

    expect(spy).toBeCalled();
    expect(spy).toBeCalledTimes(1);
  });

  test('openModalEmail function must set the correct variables', () => {
    wrapper.vm.openModalEmail();

    expect(wrapper.vm.modal.email).toBe(true);
    expect(wrapper.vm.email).toBe(propsMock.responsible.responsible_email);
  });

  describe('sendEmail', () => {
    test('should call sendEmail successfully', async () => {
      wrapper.vm.modal.email = true;

      vi.mocked(PreregistrationApi.postSendEmail).mockResolvedValueOnce(true);

      wrapper.vm.sendEmail('test@email.com');

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(wrapper.vm.modal.email).toBe(false);
    });

    test('should call sendEmail unsuccessfully', async () => {
      wrapper.vm.modal.email = true;

      vi.mocked(PreregistrationApi.postSendEmail).mockResolvedValueOnce(false);

      wrapper.vm.sendEmail('test@email.com');

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(wrapper.vm.modal.email).toBe(true);
    });
  });

  test('when I call submit function, should emit finish event', () => {
    wrapper.vm.submit();

    expect(wrapper.emitted().finish).toBeTruthy();
  });
});
