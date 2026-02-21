import {
  Fields,
  PreRegistrationResponsibleField,
  PreRegistrationStageProcess,
  PreRegistrationStudentField,
} from '@/modules/preregistration/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import Review from '@/modules/preregistration/components/Review.vue';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import filters from '@/filters';
import { expect, vi } from 'vitest';

interface ReviewProps {
  errors: { [key: string]: boolean };
  fields: Fields;
  responsible: PreRegistrationResponsibleField;
  student: PreRegistrationStudentField;
  process?: PreRegistrationStageProcess;
}

const propsMock: ReviewProps = {
  errors: {},
  fields: {
    responsible: [],
    student: [],
  },
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

const onMounted = async (props: ReviewProps) => {
  const wrapper = mount(Review, {
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

describe('Review', () => {
  let wrapper: VueWrapper<InstanceType<typeof Review>>;

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

  describe('emits', () => {
    test('when I click on edit responsible button, an event must be called', async () => {
      const btn = wrapper.find('[data-test="btn-edit-responsible"]');

      await btn.trigger('click');

      expect(wrapper.emitted()['edit-responsible']).toBeTruthy();
    });

    test('when I click on edit student button, an event must be called', async () => {
      const btn = wrapper.find('[data-test="btn-edit-student"]');

      await btn.trigger('click');

      expect(wrapper.emitted()['edit-student']).toBeTruthy();
    });

    test('when I click on another edit student button, an event must be called', async () => {
      const btn = wrapper.find('[data-test="btn-edit-student-2"]');

      await btn.trigger('click');

      expect(wrapper.emitted()['edit-student']).toBeTruthy();
    });
  });

  describe('grade', () => {
    test('should return the grade name', () => {
      const grade = wrapper.vm.grade('22');

      expect(grade).toBe(propsMock.process?.grades[0].name);
    });

    test('should not return the grade name', () => {
      const grade = wrapper.vm.grade('1');

      expect(grade).toBe(undefined);
    });
  });

  describe('period', () => {
    test('should return the period name', () => {
      const period = wrapper.vm.period('1');

      expect(period).toBe(propsMock.process?.periods[0].name);
    });

    test('should not return the period name', () => {
      const period = wrapper.vm.period('123');

      expect(period).toBe(undefined);
    });
  });

  describe('school', () => {
    test('should return the school name', () => {
      const school = wrapper.vm.school('12345');

      expect(school).toBe(propsMock.process?.schools[0].name);
    });

    test('should not return the school name', () => {
      const school = wrapper.vm.school('123');

      expect(school).toBe(undefined);
    });
  });

  describe('getCity', () => {
    test('should return the city name', () => {
      const city = wrapper.vm.getCity(
        propsMock.responsible,
        propsMock.fields.responsible
      );

      expect(city).toBe('-');
    });
  });
});
