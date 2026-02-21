import { analyse } from '@/modules/vacancy/util';
import '@/plugin/sortby';
import { flushPromises } from '@vue/test-utils';
import { Vacancy as VacancyApi, graphql } from '@/modules/vacancy/api';
import { expect, vi } from 'vitest';
import { VacancyByGradesProcessGrade, VacancyByGradesStatistics, VacancyFilterByGrade, VacancyLoadData, VacancyLoadProcess, VacancyLoadVariable, VacancyTotalProcess } from '@/modules/vacancy/types';

vi.mock('@/api');

describe('@/modules/vacancy/api/services/graphql/vacancy', () => {
  test('should list successfully', async () => {
    const vacancyTotalProcessMocked: VacancyTotalProcess[] = [
      {
        schoolYear: {
          year: 2022,
        },
        totalPreRegistrations: 22,
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          processes: {
            data: vacancyTotalProcessMocked,
          },
        },
      },
    });

    await flushPromises();

    const response = await VacancyApi.list();

    expect(response).toStrictEqual(vacancyTotalProcessMocked);
  });

  test('should listByGrades successfully', async () => {
    const vacancyFilterByGradeMocked: VacancyFilterByGrade = {
      process: 'string',
      schools: ['string'],
      grades: [22],
      periods: [22],
    };

    const vacancyByGradesProcessGradeMocked: VacancyByGradesProcessGrade[] = [
      {
        id: 'string',
        name: 'string',
        total: 22,
        available: 22,
        waiting: 22,
        accepted: 22,
        rejected: 22,
        exceded: false,
      }
    ];

    const vacancyByGradesStatisticsMocked: VacancyByGradesStatistics[] = [
      {
        accepted: 1,
        available: 1,
        grade: 'string',
        period: 'string',
        rejected: 1,
        school: 'string',
        total: 1,
        waiting: 1,
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          process: {
            grades: vacancyByGradesProcessGradeMocked,
          },
          statistics: vacancyByGradesStatisticsMocked,
        },
      },
    });

    await flushPromises();

    const response = await VacancyApi.listByGrades(vacancyFilterByGradeMocked);

    const grades = vacancyByGradesProcessGradeMocked.map((g) => ({
      ...g,
      total: 0,
      available: 0,
      waiting: 0,
      accepted: 0,
      rejected: 0,
      exceded: false,
    }));

    expect(response).toStrictEqual({
      grades,
      statistics: vacancyByGradesStatisticsMocked,
    });
  });

  test('should load successfully', async () => {
    const vacancyLoadVariableMocked:  VacancyLoadVariable = {
      schools: ['string'],
      schoolsAllowed: ['string'],
      grades: [1],
      periods: [1],
      year: 1,
    }

    const vacancyLoadDataMocked: VacancyLoadData[] = [
      {
        id: 'string',
        key: 'string',
        label: 'string',
        name: 'string',
      }
    ];

    const vacancyLoadProcessMocked: VacancyLoadProcess[] = [
      {
        process: {
          id: 'string',
          name: 'string',
        },
        accepted: 1,
        available: 1,
        excededVacancies: 1,
        rejected: 1,
        total: 1,
        waiting: 1,
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          grades: {
            data: vacancyLoadDataMocked,
          },
          periods: {
            data: vacancyLoadDataMocked,
          },
          processes: vacancyLoadProcessMocked,
          schools: {
            data: vacancyLoadDataMocked,
          },
          unique: [
            {
              process: 'string',
              unique: 1,
              waiting: 1,
            },
          ],
        },
      },
    });

    await flushPromises();

    const response = await VacancyApi.load(vacancyLoadVariableMocked);

    const processes = vacancyLoadProcessMocked.map((p) => analyse(p)).map((p) => ({
      ...p,
      ...p.process,
    })).sortBy('name');

    const unique = {
      'string': 1,
    };

    const stats = processes.reduce((v, p) => ({
      vacancies: p.total + v.vacancies,
      total: p.accepted + p.rejected + p.waiting + v.total,
      accepted: p.accepted + v.accepted,
      rejected: p.rejected + v.rejected,
      waiting: p.waiting + v.waiting,
    }), {
      vacancies: 0,
      total: 0,
      accepted: 0,
      rejected: 0,
      waiting: 0,
    });

    expect(response).toStrictEqual({
      grades: vacancyLoadDataMocked,
      periods: vacancyLoadDataMocked,
      processes,
      schools: vacancyLoadDataMocked,
      stats,
      unique,
    });
  });

});
