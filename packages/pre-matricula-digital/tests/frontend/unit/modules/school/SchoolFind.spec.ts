/// <reference types="@types/google.maps" />
import { Processes, ShowVacanciesReturn } from '@/modules/school/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import SchoolFind from '@/modules/school/components/SchoolFind.vue';
import { Schoolfind as SchoolFindApi } from '@/modules/school/api';
import { createTestingPinia } from '@pinia/testing';
import { initialize } from '../../mocks/google';
import { expect, vi } from 'vitest';

vi.mock('@/modules/school/api');

const procesesMock: Processes[] = [
  {
    id: '1',
    name: 'Processo de Testes 1',
    grades: [
      { id: '27', name: 'Nível I' },
      { id: '5', name: 'Nivel II' },
      { id: '44', name: 'Maternal' },
      { id: '12', name: 'Berçário' },
    ],
    schoolYear: {
      year: 2021,
    },
    stages: [
      {
        endAt: '2020-10-19 23:59:00',
        id: '1',
        name: 'Test',
        startAt: '2020-10-14 00:00:00',
        status: 'CLOSED',
        type: 'REGISTRATION_RENEWAL',
      },
      {
        endAt: '2020-10-27 23:59:00',
        id: '2',
        name: 'Test',
        startAt: '2020-10-22 00:00:00',
        status: 'CLOSED',
        type: 'REGISTRATION',
      },
      {
        endAt: '2020-12-18 17:00:00',
        id: '12',
        name: 'Test',
        startAt: '2020-10-23 15:00:00',
        status: 'CLOSED',
        type: 'WAITING_LIST',
      },
    ],
  },
];

const loaderVacanciesMock: ShowVacanciesReturn = {
  vacancies: [
    {
      grade: '5',
      period: '2',
      process: '1',
      school: '1',
    },
    {
      grade: '5',
      period: '4',
      process: '1',
      school: '1',
    },
    {
      grade: '12',
      period: '2',
      process: '1',
      school: '1',
    },
    {
      grade: '12',
      period: '4',
      process: '1',
      school: '1',
    },
    {
      grade: '27',
      period: '2',
      process: '1',
      school: '1',
    },
    {
      grade: '27',
      period: '4',
      process: '1',
      school: '1',
    },
    {
      grade: '44',
      period: '2',
      process: '1',
      school: '1',
    },
    {
      grade: '44',
      period: '4',
      process: '1',
      school: '1',
    },
    {
      grade: '12',
      period: '2',
      process: '1',
      school: '703011',
    },
    {
      grade: '12',
      period: '4',
      process: '1',
      school: '703011',
    },
  ],
  schools: [
    {
      area_code: 47,
      id: '0000001',
      lat: -26.257815,
      lng: -49.52137,
      name: 'Escola Municipal de Testes 1',
      phone: '9999-9999',
      position: {
        lat: -26.257815,
        lng: -49.52137,
      },
    },
    {
      area_code: 47,
      id: '0000002',
      lat: -26.264827,
      lng: -49.514835,
      name: 'Escola Municipal de Testes 2',
      phone: '9999-9999',
      position: {
        lat: -26.264827,
        lng: -49.514835,
      },
    },
    {
      area_code: 47,
      id: '0000003',
      lat: -26.24461,
      lng: -49.540354,
      name: 'Escola Municipal de Testes 3',
      phone: '9999-9999',
      position: {
        lat: -26.24461,
        lng: -49.540354,
      },
    },
    {
      area_code: 47,
      id: '0000004',
      lat: -26.227579,
      lng: -49.511398,
      name: 'Escola Municipal de Testes 4',
      phone: '9999-9999',
      position: {
        lat: -26.227579,
        lng: -49.511398,
      },
    },
    {
      area_code: 47,
      id: '0000005',
      lat: -26.269295,
      lng: -49.553098,
      name: 'Escola Municipal de Testes 5',
      phone: '9999-9999',
      position: {
        lat: -26.269295,
        lng: -49.553098,
      },
    },
  ],
};

const onMounted = async (
  success: boolean,
  proceses = procesesMock,
  loaderVacancies = loaderVacanciesMock
) => {
  if (success) {
    vi.mocked(SchoolFindApi.showProcesses).mockResolvedValueOnce(proceses);
    vi.mocked(SchoolFindApi.showVacancies).mockResolvedValueOnce(
      loaderVacancies
    );
  } else {
    vi.mocked(SchoolFindApi.showProcesses).mockRejectedValueOnce([]);
    vi.mocked(SchoolFindApi.showVacancies).mockRejectedValueOnce({});
  }

  const wrapper = mount(SchoolFind, {
    global: {
      plugins: [createTestingPinia()],
      stubs: [
        'x-field',
        'google-maps',
        'google-maps-marker-component',
        'google-maps-markers',
      ],
      mocks: {
        google: vi.fn(() => google),
      },
    },
  });

  await flushPromises();

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('SchoolFind', () => {
  let wrapper: VueWrapper<InstanceType<typeof SchoolFind>>;

  beforeEach(async () => {
    initialize();

    wrapper = await onMounted(true);

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
    expect(wrapper.vm.processes).toMatchObject(procesesMock);
    expect(wrapper.vm.vacancies).toMatchObject(loaderVacanciesMock.vacancies);
    expect(wrapper.vm.schools).toMatchObject(loaderVacanciesMock.schools);

    expect(wrapper.vm.schoolsInMap.length).toBe(5);
  });

  test('This should leave the variables empty, in case an error occurs in the request', async () => {
    wrapper = await onMounted(false);

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(wrapper.vm.processes).toMatchObject([]);
    expect(wrapper.vm.vacancies).toMatchObject([]);
    expect(wrapper.vm.schools).toMatchObject([]);

    expect(wrapper.vm.schoolsInMap.length).toBe(0);
  });

  test('should do a successful address search', async () => {
    const address = 'Rua Teste, 123';

    wrapper.vm.address = address;
    wrapper.vm.grade = '12';

    expect(wrapper.vm.address).toBe(address);
    expect(wrapper.vm.grade).toBe('12');

    const btn = wrapper.find('[data-test="btn-search-school"]');

    await btn.trigger('click');

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(wrapper.vm.schoolsInMap.length).toBe(1);
  });
});
