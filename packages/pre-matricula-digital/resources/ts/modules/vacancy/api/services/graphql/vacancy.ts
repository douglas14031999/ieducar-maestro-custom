import {
  VacancyByGradesProcessGrade,
  VacancyByGradesStatistics,
  VacancyFilterByGrade,
  VacancyListByGrades,
  VacancyLoadData,
  VacancyLoadProcess,
  VacancyLoadProcesses,
  VacancyLoadVariable,
  VacancyTotalProcess,
} from '@/modules/vacancy/types';
import { analyse } from '@/modules/vacancy/util';
import { graphql } from '@/modules/vacancy/api';

export const list = (): Promise<VacancyTotalProcess[]> => {
  const payload = {
    query: `
      query all {
        processes(first: 100) {
          data {
            totalPreRegistrations
            schoolYear {
              year
            }
          }
        }
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        processes: {
          data: VacancyTotalProcess[];
        };
      };
    };
  }>(payload)
    .then(res => res.data.data.processes.data);
};

export const listByGrades = (data: VacancyFilterByGrade): Promise<VacancyListByGrades> => {
  const payload = {
    variables: data,
    query: `
      query process(
        $process: ID!
        $schools: [ID!]
        $grades: [ID!]
        $periods: [ID!]
      ) {
        process(
          id: $process
        ) {
          grades {
            id
            name
          }
        }
        statistics: getProcessVacancyStatistics(
          process: $process
          schools: $schools
          grades: $grades
          periods: $periods
        ) {
          grade
          period
          school
          total
          waiting
          accepted
          rejected
          available: availableVacancies
        }
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        process: {
          grades: VacancyByGradesProcessGrade[];
        };
        statistics: VacancyByGradesStatistics[];
      };
    };
  }>(payload)
    .then(res => {
      const grades = res.data.data.process.grades.map((g) => ({
        ...g,
        total: 0,
        available: 0,
        waiting: 0,
        accepted: 0,
        rejected: 0,
        exceded: false,
      }));
      
      return {
        grades,
        statistics: res.data.data.statistics,
      }
    });
};

export const load = (data: VacancyLoadVariable): Promise<VacancyLoadProcesses> => {
  const payload = {
    variables: data,
    query: `
      query processes(
        $schools: [ID!]
        $schoolsAllowed: [ID!]
        $grades: [ID!]
        $periods: [ID!]
        $year: Int
      ) {
        processes: getProcessVacancyTotal(
          schools: $schools
          grades: $grades
          periods: $periods
          year: $year
        ) {
          process {
            id
            name
          }
          total
          available: availableVacancies
          waiting
          rejected
          accepted
          excededVacancies
        }
        schools(
          first: 200
          schools: $schoolsAllowed
        ) {
          data {
            id
            name
            key: id
            label: name
          }
        }
        grades(
          first: 100
        ) {
          data {
            id
            name
            key: id
            label: name
          }
        }
        periods {
          data {
            id
            name
            key: id
            label: name
          }
        }
        unique: getProcessVacancyUnique(
          schools: $schools
          grades: $grades
          periods: $periods
          year: $year
        ) {
          process
          unique
          waiting
        }
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        grades: {
          data: VacancyLoadData[];
        };
        periods: {
          data: VacancyLoadData[];
        };
        processes: VacancyLoadProcess[];
        schools: {
          data: VacancyLoadData[];
        };
        unique: [
          {
            process: number;
            unique: number;
            waiting: number;
          }
        ]
      };
    };
  }>(payload)
    .then(res => {
      const processes = res.data.data.processes.map((p) => analyse(p)).map((p) => ({
        ...p,
        ...p.process,
      })).sortBy('name');

      const unique = res.data.data.unique.reduce((previous, current) => ({
        ...previous,
        [current.process]: current.unique,
      }), {});

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

      return {
        grades: res.data.data.grades.data,
        periods: res.data.data.periods.data,
        processes,
        schools: res.data.data.schools.data,
        stats,
        unique,
      };
    });
};
