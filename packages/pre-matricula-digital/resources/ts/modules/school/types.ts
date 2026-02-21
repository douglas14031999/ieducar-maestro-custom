import { SchoolYear, Stages } from '@/types';
export interface Grade {
  id: string;
  name: string;
}
export interface GradeFiltered {
  key: string;
  label: string;
}
export interface Processes {
  id: string;
  name: string;
  grades: Grade[];
  schoolYear: SchoolYear;
  stages: Stages[];
}
export interface SchoolInfo {
  id: string;
  name: string;
  lat: number;
  lng: number;
  area_code: number;
  phone: string;
}
export interface School extends SchoolInfo {
  title?: string;
  position: {
    lat: number;
    lng: number;
  };
}
export interface Vacancies {
  grade: string;
  period: string;
  process: string;
  school: string;
}

export interface ShowVacanciesReturn {
  vacancies: Vacancies[];
  schools: School[];
}
