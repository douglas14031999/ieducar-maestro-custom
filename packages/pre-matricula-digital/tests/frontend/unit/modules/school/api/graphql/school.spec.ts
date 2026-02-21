import { flushPromises } from '@vue/test-utils';
import { Schoolfind as SchoolfindApi, graphql } from '@/modules/school/api';
import { expect, vi } from 'vitest';
import { Grades, Processes, SchoolYear, Stages } from '@/types';
import { SchoolInfo, Vacancies } from '@/modules/school/types';

vi.mock('@/api');

describe('@/modules/school/api/services/graphql/school', () => {
  test('should showProcesses successfully', async () => {
    const gradesMocked: Grades[] = [
      {
        id: 1,
        name: 'string',
        label: 'string',
        key: 2,
      }
    ];

    const stagesMocked: Stages[] = [
      {
        endAt: 'string',
        id: 'string',
        name: 'string',
        startAt: 'string',
        status: 'string',
        type: 'string',
      }
    ];

    const schoolYearMoked: SchoolYear = {
      year: 2022,
    }

    const processesMoked: Processes[] = [
      {
        id: 'string',
        name: 'string',
        schoolYear: schoolYearMoked,
        stages: stagesMocked,
        grades: gradesMocked,
        startAt: 'string',
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          processes: {
            data: processesMoked,
          },
        },
      },
    });

    await flushPromises();

    const response = await SchoolfindApi.showProcesses();

    expect(response).toStrictEqual(processesMoked);
  });

  test('should showVacancies successfully', async () => {
    const processesMocked = {
      processes: ['string'],
    };

    const vacanciesMocked: Vacancies[] = [
      {
        grade: 'string',
        period: 'string',
        process: 'string',
        school: 'string',
      }
    ];

    const schoolInfoMocked: SchoolInfo[] = [
      {
        id: 'string',
        name: 'string',
        lat: 1,
        lng: 1,
        area_code: 1,
        phone: 'string',
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          vacancies: vacanciesMocked,
          schools: {
            data: schoolInfoMocked,
          },
        },
      },
    });

    await flushPromises();

    const response = await SchoolfindApi.showVacancies(processesMocked);

    expect(response).toStrictEqual({
      vacancies: vacanciesMocked,
      schools: schoolInfoMocked.map((school) => ({
        ...school,
        position: {
          lat: school.lat,
          lng: school.lng,
        },
      })),
    });
  });

});