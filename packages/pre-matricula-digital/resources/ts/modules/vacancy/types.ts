import { Nullable } from '@/types';

export interface VacancyTotalProcess {
  schoolYear: {
    year: number;
  };
  totalPreRegistrations: number;
}

export interface VacancyFilter {
  school: Nullable<string>;
  period: Nullable<number>;
  grade: Nullable<number>;
  year: Nullable<number>;
  canAcceptInBatch: boolean;
}

export interface VacancyLoadVariable {
  schools: string[];
  schoolsAllowed: string[];
  grades: number[];
  periods: number[];
  year: number;
}

export interface VacancyLoadData {
  id: string;
  key: string;
  label: string;
  name: string;
}

export interface VacancyLoadProcess {
  process: {
    id: string;
    name: string;
  };
  accepted: number;
  available: number;
  excededVacancies: number;
  rejected: number;
  total: number;
  waiting: number;
}

export interface VacancyStats {
  vacancies: number;
  total: number;
  accepted: number;
  rejected: number;
  waiting: number;
}

export interface VacancyUnique {
  [process: string]: number;
}

export interface GraphFormatter {
  globals: {
    seriesTotals: number[];
    labels: string[];
  };
}

export interface GraphCustomParam {
  w: GraphFormatter;
  series: number[];
  seriesIndex: number;
}

export interface VacancyLoadProcessAnalyse extends VacancyLoadProcess {
  id: string;
  name: string;
  unavailable: number;
  exceded: number;
  ultrapassed: number;
  percentual: {
    accepted: number;
    available: number;
    unavailable: number;
    waitingAvailable: number;
    waitingExceded: number;
    maxWaiting?: number;
    waitingRealAvailable: number;
    total: number;
    totalExceded: number;
    exceded: number;
    waiting: number;
  };
  max?: number;
  totalExceded?: number;
  waitingAvailable?: number;
  waitingExceded?: number;
  maxWaiting?: number;
}

export interface VacancyFilterByGrade {
  process: string;
  schools: string[];
  grades: number[];
  periods: number[];
}

export interface VacancyByGradesProcessGrade {
  id: string;
  name: string;
  total: number;
  available: number;
  waiting: number;
  accepted: number;
  rejected: number;
  exceded: boolean;
}

export interface VacancyByGradesStatistics {
  accepted: number;
  available: number;
  grade: string;
  period: string;
  rejected: number;
  school: string;
  total: number;
  waiting: number;
}

export interface VacancyLoadProcesses {
  grades: VacancyLoadData[];
  periods: VacancyLoadData[];
  processes: VacancyLoadProcessAnalyse[];
  schools: VacancyLoadData[];
  stats: VacancyStats;
  unique: VacancyUnique;
}

export interface VacancyListByGrades {
  grades: VacancyByGradesProcessGrade[];
  statistics: VacancyByGradesStatistics[];
}
