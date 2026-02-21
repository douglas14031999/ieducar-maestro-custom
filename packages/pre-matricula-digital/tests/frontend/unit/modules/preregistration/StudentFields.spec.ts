/// <reference types="@types/google.maps" />
import '@/plugin/sortby';
import {
  ParseFieldFromProcess,
  PreRegistrationResponsibleField,
  PreRegistrationStage,
  PreRegistrationStudentField,
} from '@/modules/preregistration/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { expect, vi } from 'vitest';
import { Rules } from '@/types';
import StudentFields from '@/modules/preregistration/components/StudentFields.vue';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import filters from '@/filters';
import { initialize } from '../../mocks/google';

interface StudentFieldsProps {
  student: PreRegistrationStudentField;
  stage: PreRegistrationStage;
  responsible: PreRegistrationResponsibleField;
  fields: ParseFieldFromProcess[];
  errors: Rules;
}

const propsMock: StudentFieldsProps = {
  student: {
    grade: null,
    period: null,
    school: null,
    useSecondSchool: false,
    secondSchool: null,
    secondPeriod: null,
    match: null,
    student_name: null,
    student_date_of_birth: '2015-06-01T00:00:00.000Z',
    student_city_of_birth: 'string',
    student_cpf: 'string',
    student_rg: 'string',
    student_marital_status: 1,
    student_place_of_birth: 1,
    student_birth_certificate: 'string',
    student_gender: 1,
    student_email: 'string',
    student_phone: 'string',
    student_mobile: 'string',
    waitingList: [],
  },
  stage: {
    restrictionType: 'NONE',
    allowWaitingList: true,
    id: '1',
    process: {
      onePerYear: false,
      waitingListLimit: 9,
      minimumAge: null,
      forceSuggestedGrade: true,
      gradeAgeRangeLink: null,
      id: '1',
      messageFooter:
        'Acompanhe a publicação do resultado classificatório e convocação no site da Prefeitura.',
      name: 'PROCESSO DE TESTES 2022',
      showPriorityProtocol: true,
      allowResponsibleSelectMapAddress: false,
      blockIncompatibleAgeGroup: false,
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
          latitude: 1,
          longitude: 1,
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
            description: null
          },
          id: '262',
          order: 1,
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
            description: null
          },
          id: '267',
          order: 2,
          required: true,
          weight: 0,
        },
      ],
    },
    radius: 1000,
    renewalAtSameSchool: false,
    status: 'OPEN',
    type: 'REGISTRATION',
  },
  responsible: {
    relationType: null,
    address: {
      postalCode: '',
      address: '',
      number: '',
      complement: '',
      neighborhood: '',
      lat: 1,
      lng: 1,
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
      lat: 1,
      lng: 1,
      city: '',
      cityIbgeCode: 0,
      stateAbbreviation: '',
    },
    responsible_name: null,
  } as PreRegistrationResponsibleField,
  fields: [],
  errors: {},
};

const onMounted = async (props: StudentFieldsProps) => {
  const wrapper = mount(StudentFields, {
    props,
    global: {
      plugins: [createTestingPinia()],
      stubs: [
        'x-field',
        'google-maps',
        'google-maps-markers',
        'google-maps-marker-component',
      ],
      directives: {
        tooltip: VTooltip,
      },
      provide: {
        $filters: filters,
      },
      mocks: {
        google: vi.fn(() => google),
      },
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('StudentFields', () => {
  let wrapper: VueWrapper<InstanceType<typeof StudentFields>>;

  beforeEach(async () => {
    initialize();

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

  describe('components', () => {
    test('must have 1 fields component', () => {
      expect(
        wrapper.findAllComponents({
          name: 'fields',
        }).length
      ).toBe(1);
    });

    test('primarily, must have 2 x-field components', () => {
      expect(wrapper.findAll('x-field-stub').length).toBe(4);
    });
  });

  describe('methods', () => {
    beforeEach(async () => {
      initialize();

      wrapper = await onMounted(propsMock);

      await wrapper.vm.$nextTick();
    });

    afterEach(() => {
      vi.clearAllMocks();
    });

    test('clearSchools', () => {
      wrapper.vm.clearSchools();

      expect(wrapper.vm.modelStudent.school).toBeNull();
      expect(wrapper.vm.modelStudent.secondSchool).toBeNull();
      expect(wrapper.vm.modelStudent.secondPeriod).toBeNull();
    });

    test('doesntuseSecondSchool', () => {
      wrapper.vm.modelStudent.useSecondSchool = true;
      wrapper.vm.modelStudent.secondSchool = '1';
      wrapper.vm.modelStudent.secondPeriod = '1';

      wrapper.vm.doesntuseSecondSchool();

      expect(wrapper.vm.modelStudent.useSecondSchool).toBeFalsy();
      expect(wrapper.vm.modelStudent.secondSchool).toBeNull();
      expect(wrapper.vm.modelStudent.secondPeriod).toBeNull();
    });

    test('selectFirstSchool', () => {
      wrapper.vm.selectFirstSchool(propsMock.stage.process.schools[0]);

      expect(wrapper.vm.modelStudent.school).toBe(
        propsMock.stage.process.schools[0].id
      );
    });

    test('selectFirstSchool must to be a default student.school', () => {
      wrapper.vm.selectFirstSchool();

      expect(wrapper.vm.modelStudent.school).toBe(propsMock.student.school);
    });

    test('selectSecondSchool', () => {
      wrapper.vm.selectSecondSchool(propsMock.stage.process.schools[0]);

      expect(wrapper.vm.modelStudent.secondSchool).toBe(
        propsMock.stage.process.schools[0].id
      );
    });

    test('selectSecondSchool must to be a default student.secondSchool', () => {
      wrapper.vm.selectSecondSchool();

      expect(wrapper.vm.modelStudent.secondSchool).toBe(
        propsMock.student.secondSchool
      );
    });

    test('suggestGrade', () => {
      wrapper.vm.suggestGrade(propsMock.student.student_date_of_birth);

      expect(wrapper.vm.suggestedGrades).toMatchObject([
        propsMock.stage.process.grades[0],
      ]);
    });

    test('suggestGrade must to be empty', () => {
      wrapper.vm.suggestGrade('2020-01-01');

      expect(wrapper.vm.suggestedGrades).toHaveLength(0);
    });

    test('isMatchedSchool', async () => {
      wrapper = await onMounted({
        ...propsMock,
        student: {
          ...propsMock.student,
          match: {
            id: 1,
            type: 'NONE',
            initials: 'string',
            dateOfBirth: 'string',
            registration: {
              year: 2024,
              school: {
                id: 12345,
                name: 'School 12345',
              },
              grade: {
                id: 102030,
                name: 'Grade 102030',
              },
            },
          },
        },
      });

      const response = wrapper.vm.isMatchedSchool('12345');

      expect(response).toBeTruthy();
    });

    test('isMatchedSchool must return false value', async () => {
      wrapper = await onMounted({
        ...propsMock,
        student: {
          ...propsMock.student,
          match: {
            id: 1,
            type: 'NONE',
            initials: 'string',
            dateOfBirth: 'string',
            registration: {
              year: 2024,
              school: {
                id: 98765,
                name: 'School 98765',
              },
              grade: {
                id: 102030,
                name: 'Grade 102030',
              },
            },
          },
        },
      });

      const response = wrapper.vm.isMatchedSchool('12345');

      expect(response).toBeFalsy();
    });

    test('isMatchedSchool should not return null value', async () => {
      const response = wrapper.vm.isMatchedSchool('12345');

      expect(response).not.toBeNull();
      expect(response).toBeFalsy();
    });

    test('isWithinRange', () => {
      const response = wrapper.vm.isWithinRange(
        propsMock.stage.process.schools[0]
      );

      expect(response).toBeTruthy();
    });

    test('isWithinRange must return false', async () => {
      wrapper = await onMounted({
        ...propsMock,
        stage: {
          ...propsMock.stage,
          process: {
            ...propsMock.stage.process,
            schools: [],
          },
        },
      });

      await wrapper.vm.$nextTick();

      const response = wrapper.vm.isWithinRange({
        id: '1',
        latitude: 12,
        longitude: 43,
        name: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL II',
      });

      expect(response).toBeFalsy();
    });

    test('setUseSecondSchool', async () => {
      wrapper.vm.setUseSecondSchool();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.modelStudent.useSecondSchool).toBeTruthy();
      expect(wrapper.vm.modelStudent.secondPeriod).toBe(
        propsMock.student.period
      );
    });

    test('handleSetNewLocation', async () => {
      wrapper.vm.handleSetNewLocation();

      expect(wrapper.emitted()['change-primary-address']).toBeTruthy();
      expect(wrapper.emitted()['change-primary-address'][0]).toStrictEqual([
        {
          changed: true,
          secondAddress: false,
        },
      ]);
    });

    test('should not emit if handleSetNewLocation was not triggered', async () => {
      expect(wrapper.emitted()['change-primary-address']).toBeFalsy();
    });

    test('handleCancelNewLocation ', async () => {
      wrapper.vm.handleCancelNewLocation();

      expect(wrapper.emitted()['change-primary-address']).toBeTruthy();
      expect(wrapper.emitted()['change-primary-address'][0]).toStrictEqual([
        {
          changed: false,
          secondAddress: false,
        },
      ]);
    });

    test('should not emit if handleCancelNewLocation  was not triggered', async () => {
      expect(wrapper.emitted()['change-primary-address']).toBeFalsy();
    });

    test('handleUndoAddressPosition if secondAddress', async () => {
      const spyHandleUpdateModelResponsible = vi.spyOn(
        wrapper.vm.ctx,
        'handleUpdateModelResponsible'
      );
      const spyHandleUpdateSelectedAddress = vi.spyOn(
        wrapper.vm.ctx,
        'handleUpdateSelectedAddress'
      );

      const old = {
        secondAddress: true,
        lat: 0,
        lng: 0,
      };

      wrapper.vm.handleUndoAddressPosition(old);

      expect(spyHandleUpdateModelResponsible).toHaveBeenCalledTimes(1);
      expect(spyHandleUpdateModelResponsible).toHaveBeenCalledWith(
        old,
        'secondAddress'
      );

      expect(spyHandleUpdateSelectedAddress).toHaveBeenCalledTimes(1);
      expect(spyHandleUpdateSelectedAddress).toHaveBeenCalledWith(
        wrapper.vm.modelResponsible.secondAddress
      );

      expect(wrapper.emitted()['change-primary-address']).toBeTruthy();
      expect(wrapper.emitted()['change-primary-address'][0]).toStrictEqual([
        {
          changed: false,
          secondAddress: old.secondAddress,
        },
      ]);
    });

    test('handleUndoAddressPosition else address', async () => {
      const spyHandleUpdateModelResponsible = vi.spyOn(
        wrapper.vm.ctx,
        'handleUpdateModelResponsible'
      );
      const spyHandleUpdateSelectedAddress = vi.spyOn(
        wrapper.vm.ctx,
        'handleUpdateSelectedAddress'
      );

      const old = {
        secondAddress: false,
        lat: 0,
        lng: 0,
      };

      wrapper.vm.handleUndoAddressPosition(old);

      expect(spyHandleUpdateModelResponsible).toHaveBeenCalledTimes(1);
      expect(spyHandleUpdateModelResponsible).toHaveBeenCalledWith(
        old,
        'address'
      );

      expect(spyHandleUpdateSelectedAddress).toHaveBeenCalledTimes(1);
      expect(spyHandleUpdateSelectedAddress).toHaveBeenCalledWith(
        wrapper.vm.modelResponsible.address
      );

      expect(wrapper.emitted()['change-primary-address']).toBeTruthy();
      expect(wrapper.emitted()['change-primary-address'][0]).toStrictEqual([
        {
          changed: false,
          secondAddress: old.secondAddress,
        },
      ]);
    });

    test('handleConfirmNewLocation if secondAddress true', () => {
      const old = {
        secondAddress: true,
        lat: 0,
        lng: 0,
      };

      const adjusted = {
        secondAddress: true,
        lat: 1,
        lng: 1,
      };

      wrapper.vm.handleConfirmNewLocation(old, adjusted);

      expect(wrapper.vm.modelResponsible.secondAddress).toStrictEqual({
        ...wrapper.vm.modelResponsible.secondAddress,
        lat: adjusted.lat,
        lng: adjusted.lng,
      });

      expect(wrapper.vm.selectedAddress).toBe(
        wrapper.vm.modelResponsible.secondAddress
      );

      expect(wrapper.emitted()['change-primary-address']).toBeTruthy();
      expect(wrapper.emitted()['change-primary-address'][0]).toStrictEqual([
        {
          changed: true,
          secondAddress: old.secondAddress,
        },
      ]);
    });

    test('handleConfirmNewLocation if secondAddress false', () => {
      const old = {
        secondAddress: false,
        lat: 0,
        lng: 0,
      };

      const adjusted = {
        secondAddress: false,
        lat: 1,
        lng: 1,
      };

      wrapper.vm.handleConfirmNewLocation(old, adjusted);

      expect(wrapper.vm.modelResponsible.address).toStrictEqual({
        ...wrapper.vm.modelResponsible.address,
        lat: adjusted.lat,
        lng: adjusted.lng,
      });

      expect(wrapper.vm.selectedAddress).toBe(
        wrapper.vm.modelResponsible.address
      );

      expect(wrapper.emitted()['change-primary-address']).toBeTruthy();
      expect(wrapper.emitted()['change-primary-address'][0]).toStrictEqual([
        {
          changed: true,
          secondAddress: old.secondAddress,
        },
      ]);
    });
  });

  describe('template', () => {
    it('when matchedSchool and matchedGrade exists, the alert wich shows the student is already registered must be shown', async () => {
      wrapper = await onMounted({
        ...propsMock,
        student: {
          ...propsMock.student,
          // school: '12345',
          match: {
            id: 1,
            type: 'NONE',
            initials: 'string',
            dateOfBirth: 'string',
            registration: {
              year: 2024,
              school: {
                id: 12345,
                name: 'School 12345',
              },
              grade: {
                id: 22,
                name: 'Grade 22',
              },
            },
          },
        },
      });

      const element = wrapper.find(
        '[data-test="alert-matched-school-and-grade"]'
      );

      expect(element.exists()).toBeTruthy();

      const span = wrapper.find(
        '[data-test="alert-matched-school-and-grade-already-selected"]'
      );

      expect(span.exists()).toBeTruthy();
    });

    it('when matchedSchool and matchedGrade exists, and matchedSchool.id is different of modelStudent.school, the [data-test="alert-matched-school-and-grade-already-selected"] must be falsy', async () => {
      wrapper = await onMounted({
        ...propsMock,
        stage: {
          ...propsMock.stage,
          process: {
            ...propsMock.stage.process,
            schools: [
              {
                ...propsMock.stage.process.schools[0],
                id: '123456',
              },
            ],
          },
        },
        student: {
          ...propsMock.student,
          match: {
            id: 1,
            type: 'NONE',
            initials: 'string',
            dateOfBirth: 'string',
            registration: {
              year: 2024,
              school: {
                id: 12345,
                name: 'School 12345',
              },
              grade: {
                id: 22,
                name: 'Grade 22',
              },
            },
          },
        },
      });

      const span = wrapper.find(
        '[data-test="alert-matched-school-and-grade-already-selected"]'
      );

      expect(span.exists()).toBeFalsy();
    });

    it('when matchedSchool and matchedGrade not exists, the alert wich shows the student is already registered must not exist', async () => {
      wrapper = await onMounted(propsMock);

      const element = wrapper.find(
        '[data-test="alert-matched-school-and-grade"]'
      );

      expect(element.exists()).toBeFalsy();
    });

    it('when I click in [data-test="clear-student-data"], a clean emit must be called', async () => {
      const buttonClean = wrapper.find('[data-test="clear-student-data"]');

      expect(buttonClean.exists()).toBeTruthy();

      await buttonClean.trigger('click');

      expect(wrapper.emitted()['clean']).toBeTruthy();
      expect(wrapper.emitted()['clean'][0]).toStrictEqual([]);
    });
  });

  describe('watch', () => {
    beforeEach(async () => {
      initialize();

      wrapper = await onMounted(propsMock);

      await wrapper.vm.$nextTick();
    });

    afterEach(() => {
      vi.clearAllMocks();
    });

    it('modelStudent.value.student_date_of_birth', async () => {
      const spySuggestGrade = vi.spyOn(wrapper.vm.ctx, 'suggestGrade');

      expect(spySuggestGrade).not.toHaveBeenCalled();

      wrapper.vm.modelStudent.student_date_of_birth = '2020-01-01';

      await wrapper.vm.$nextTick();

      expect(spySuggestGrade).toHaveBeenCalledTimes(1);
      expect(spySuggestGrade).toHaveBeenCalledWith('2020-01-01');
    });

    it('modelStudent.value.grade', async () => {
      const spyClearSchools = vi.spyOn(wrapper.vm.ctx, 'clearSchools');

      expect(spyClearSchools).not.toHaveBeenCalled();

      wrapper.vm.modelStudent.grade = '1';

      await wrapper.vm.$nextTick();

      expect(spyClearSchools).toHaveBeenCalledTimes(1);
      expect(spyClearSchools).toHaveBeenCalledWith();
    });

    it('modelStudent.value.period', async () => {
      const spyClearSchools = vi.spyOn(wrapper.vm.ctx, 'clearSchools');

      expect(spyClearSchools).not.toHaveBeenCalled();

      wrapper.vm.modelStudent.period = '1';

      await wrapper.vm.$nextTick();

      expect(spyClearSchools).toHaveBeenCalledTimes(1);
      expect(spyClearSchools).toHaveBeenCalledWith();
    });

    it('grades with length equal 1', async () => {
      expect(wrapper.vm.grades).toStrictEqual([
        {
          key: '22',
          label: '1º Ano',
        },
      ]);

      wrapper.vm.stage.process.grades = [
        {
          ...propsMock.stage.process.grades[0],
          id: '23',
        },
      ];

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.modelStudent.grade).toBe(
        propsMock.stage.process.grades[0].id
      );
    });

    it('grades with length different of 1', async () => {
      expect(wrapper.vm.grades).toStrictEqual([
        {
          key: '23',
          label: '1º Ano',
        },
      ]);

      wrapper.vm.stage.process.grades = [];

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.modelStudent.grade).toBeNull();
    });
  });

  describe('computed', () => {
    beforeEach(async () => {
      initialize();

      wrapper = await onMounted(propsMock);

      await wrapper.vm.$nextTick();
    });

    afterEach(() => {
      vi.clearAllMocks();
    });

    it("'grades' when blockIncompatibleAgeGroup is true and suggestedGrades is empty", () => {
      wrapper.vm.stage.process.blockIncompatibleAgeGroup = true;
      wrapper.vm.suggestedGrades = [];

      expect(wrapper.vm.grades).toStrictEqual([]);
    });

    it("'showWaitingListOption' should return true, when stage.type = 'WAITING_LIST'", () => {
      wrapper.vm.stage.type = 'WAITING_LIST';

      expect(wrapper.vm.showWaitingListOption).toBeTruthy();
    });

    it("'doesntHaveClosestSchools' should return false, when renewalAtSameSchool is true and isWaitingList is false", async () => {
      wrapper.vm.stage.type = 'REGISTRATION';
      wrapper.vm.stage.renewalAtSameSchool = true;

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.doesntHaveClosestSchools).toBeFalsy();
    });

    it("'schoolsWithVacancy' should return true with propsMock default setup", () => {
      expect(wrapper.vm.schoolsWithVacancy).toStrictEqual([
        {
          id: '12345',
          latitude: 1,
          longitude: 1,
          name: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
          key: '12345',
          label: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
          lat: 1,
          lng: 1,
          position: { lat: 1, lng: 1 },
        },
      ]);
    });

    it("'schoolsWithVacancy' should return empty array when grade vacancy is different of grade modelStudent", async () => {
      wrapper.vm.student.period = '2';
      wrapper.vm.student.grade = '7';

      expect(wrapper.vm.schoolsWithVacancy).toStrictEqual([]);
    });

    it("'schoolsWithVacancy' should return empty array when grade vacancy is different of grade modelStudent", async () => {
      wrapper.vm.student.grade = '6';
      wrapper.vm.student.period = '2';

      expect(wrapper.vm.schoolsWithVacancy).toStrictEqual([]);
    });

    it("'schoolsWithVacancy' should return a expected array when none of the ifs are entered", async () => {
      wrapper.vm.student.grade = '6';
      wrapper.vm.student.period = '1';

      expect(wrapper.vm.schoolsWithVacancy).toStrictEqual([
        {
          id: '12345',
          latitude: 1,
          longitude: 1,
          name: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
          key: '12345',
          label: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
          lat: 1,
          lng: 1,
          position: { lat: 1, lng: 1 },
        },
      ]);
    });

    it("'allOptionalsSchools' should return empty array when grade vacancy is different of grade modelStudent", () => {
      wrapper.vm.student.grade = '1';
      wrapper.vm.student.secondPeriod = '1';

      expect(wrapper.vm.getOptionalSchoolsFilter('1', '1', null)).toStrictEqual([]);
    });

    it("'allOptionalsSchools' should return a expected array when none of the ifs are entered", () => {
      wrapper.vm.student.grade = '6';
      wrapper.vm.student.secondPeriod = '1';

      expect(wrapper.vm.getOptionalSchoolsFilter('6', '1', null)).toStrictEqual([
        {
          id: '12345',
          latitude: 1,
          longitude: 1,
          name: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
          key: '12345',
          label: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
          lat: 1,
          lng: 1,
          position: { lat: 1, lng: 1 },
        },
      ]);
    });

    it("'filteredSchools' should return expected array when filter returns valid value", async () => {
      wrapper.vm.student.grade = null;
      wrapper.vm.student.period = null;
      wrapper.vm.student.match = {
        id: 1,
        type: 'NONE',
        initials: 'string',
        dateOfBirth: 'string',
        registration: {
          year: 2024,
          school: {
            id: 12345,
            name: 'School 12345',
          },
          grade: {
            id: 102030,
            name: 'Grade 102030',
          },
        },
      };

      expect(wrapper.vm.filteredSchools).toStrictEqual([
        {
          id: '12345',
          latitude: 1,
          longitude: 1,
          name: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
          key: '12345',
          label: 'ESCOLA MUNICIPAL DE ENSINO FUNDAMENTAL',
          lat: 1,
          lng: 1,
          position: { lat: 1, lng: 1 },
        },
      ]);
    });

    it("'filteredSchools' should return an empty array if no schools are listed", () => {
      wrapper.vm.stage.process.schools = [];

      expect(wrapper.vm.filteredSchools).toStrictEqual([]);
    });

    it("'filteredSchools' should return an empty array if for some reason isWithinRange returns false and the filter returns empty array", () => {
      wrapper.vm.stage.renewalAtSameSchool = true;

      expect(wrapper.vm.filteredSchools).toStrictEqual([]);
    });

    it('markerOptionalSchools', async () => {
      wrapper = await onMounted({
        ...propsMock,
        student: {
          ...propsMock.student,
          school: '12345',
        },
        stage: {
          ...propsMock.stage,
          process: {
            ...propsMock.stage.process,
            schools: [
              {
                id: '123456',
                name: 'string',
                latitude: 1,
                longitude: 1,
              },
            ],
          },
        },
      });

      wrapper.vm.student.grade = null;
      wrapper.vm.student.secondPeriod = null;

      const schools = wrapper.vm.getOptionalSchoolsFilter(null, null, null);
      const markers = wrapper.vm.markerOptionalSchools(schools);

      expect(markers).toStrictEqual([
        {
          label: 'string',
          position: { lat: 1, lng: 1 },
          id: '123456',
        },
      ]);
    });

    it('markerAdjustedAddress', () => {
      wrapper.vm.selectedAddress.lat = 2;
      wrapper.vm.selectedAddress.lng = 2;

      expect(wrapper.vm.markerAdjustedAddress).toStrictEqual({
        id: 1,
        title: 'Seu endereço',
        position: { lat: 2, lng: 2 },
        config: {
          icon: {
            path: 'M 0, 0 m -5, 0 a 5,5 0 1,0 10,0 a 5,5 0 1,0 -10,0',
            strokeOpacity: 0.7,
            strokeWeight: 4,
            strokeColor: 'rgb(0,155,77)',
            fillColor: 'rgb(255,255,255)',
            fillOpacity: 0.7,
            scale: 1,
          },
        },
      });
    });

    it('marker', () => {
      wrapper.vm.selectedAddress.lat = 2;
      wrapper.vm.selectedAddress.lng = 2;

      expect(wrapper.vm.marker).toStrictEqual({
        id: 1,
        title: 'Seu endereço',
        position: { lat: 2, lng: 2 },
        config: {
          icon: {
            path: 'M 0, 0 m -5, 0 a 5,5 0 1,0 10,0 a 5,5 0 1,0 -10,0',
            strokeOpacity: 0.7,
            strokeWeight: 4,
            strokeColor: 'rgb(0,155,77)',
            fillColor: 'rgb(255,255,255)',
            fillOpacity: 0.7,
            scale: 1,
          },
        },
      });
    });

    it('markerClosestSchools', async () => {
      wrapper.vm.stage.process.schools = [
        {
          id: '12345',
          name: 'string',
          latitude: 1,
          longitude: 1,
        },
      ];

      wrapper.vm.student.grade = null;
      wrapper.vm.student.period = null;
      wrapper.vm.modelStage.renewalAtSameSchool = false;

      expect(wrapper.vm.markerClosestSchools).toStrictEqual([
        {
          id: '12345',
          latitude: 1,
          longitude: 1,
          name: 'string',
          key: '12345',
          label: 'string',
          lat: 1,
          lng: 1,
          position: { lat: 1, lng: 1 },
        },
      ]);
    });
  });
});
