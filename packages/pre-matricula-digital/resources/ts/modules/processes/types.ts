import { ErrorResponse, ID, IdName, Nullable, Option } from '@/types';
import { ProcessField, ProcessFieldField } from '../preregistration/types';

export type ProcessType =
  | 'REGISTRATION_RENEWAL'
  | 'REGISTRATION'
  | 'WAITING_LIST';

export type ProcessStageRestriction =
  | 'NONE'
  | 'REGISTRATION_LAST_YEAR'
  | 'REGISTRATION_CURRENT_YEAR'
  | 'NO_REGISTRATION_CURRENT_YEAR'
  | 'NO_REGISTRATION_PERIOD_CURRENT_YEAR'
  | 'NEW_STUDENT'
  | 'PRE_REGISTRATION';

export type ProcessRejectType =
  | 'NO_REJECT'
  | 'REJECT_SAME_PROCESS'
  | 'REJECT_SAME_YEAR';

export interface SchoolYear {
  year: number;
}

export interface Stage {
  endAt: string;
  id: string;
  name: string;
  startAt: string;
  type: ProcessType;
  status: string;
  observation?: string;
  totalWaitingPreRegistrations?: number;
}

export interface Process {
  id: string;
  name: string;
  active: boolean;
  key: string;
  label: string;
  schoolYear: SchoolYear;
  stages: Stage[];
}

export interface Grade {
  name: string;
}

export interface Period {
  id?: string;
  name: string;
}

export interface School {
  name: string;
}

export interface Student {
  name: string;
}

export interface Responsible {
  name: string;
}

export interface Preregistration {
  id: string;
  parent: Nullable<string>;
  period: Period;
  position: number;
  protocol: string;
  school: School;
  schoolYear: Nullable<SchoolYear>;
  status: string;
  student: Student;
  responsible: Responsible;
  type: ProcessType;
  waiting: Nullable<Preregistration>;
}

export interface PreregistrationPaginator {
  count: number;
  currentPage: number;
  lastPage: number;
  perPage: number;
  total: number;
}

export interface ProcessCount {
  accepted: number;
  id: string;
  rejected: number;
  total: number;
  vacancies: number;
  waiting: number;
}

export interface ProcessCheck {
  id: string;
  name: string;
  fields: ProcessField[];
  grades: {
    endBirth: string;
    id: string;
    name: string;
    startBirth: string;
  }[];
  periods: Period[];
  schoolYear: SchoolYear;
  schools: {
    id: string;
    latitude: number;
    longitude: number;
    name: string;
  }[];
  stages: Stage[];
  vacancies: {
    available: number;
    grade: string;
    period: string;
    school: string;
    total: number;
  }[];
}

export interface ProcessCreate {
  active: boolean;
  forceSuggestedGrade: boolean;
  gradeAgeRangeLink: Nullable<string>;
  grades: string[];
  id: Nullable<string>;
  messageFooter: string;
  name: Nullable<string>;
  periods: string[];
  selectedSchools: boolean;
  schools: string[];
  schoolYear: Nullable<string>;
  showPriorityProtocol: boolean;
  allowResponsibleSelectMapAddress: boolean;
  blockIncompatibleAgeGroup: boolean;
  autoRejectByDays: boolean;
  autoRejectDays: Nullable<number>;
  waitingListLimit: number;
  rejectType: ProcessRejectType;
  minimumAge: Nullable<number>;
  onePerYear: boolean;
  showWaitingList: boolean;
  criteria: Nullable<string>;
  grouper: Nullable<string>;
}

export interface ProcessCreateResponse {
  active: boolean;
  forceSuggestedGrade: boolean;
  gradeAgeRangeLink: Nullable<string>;
  grades: ID[];
  id: Nullable<string>;
  messageFooter: string;
  name: Nullable<string>;
  periods: ID[];
  selectedSchools: boolean;
  schools: ID[];
  schoolYear: {
    id: string;
  };
  showPriorityProtocol: boolean;
  allowResponsibleSelectMapAddress: boolean;
  blockIncompatibleAgeGroup: boolean;
  autoRejectByDays: boolean;
  autoRejectDays: Nullable<number>;
  waitingListLimit: number;
  rejectType: ProcessRejectType;
  minimumAge: Nullable<number>;
  onePerYear: boolean;
  showWaitingList: boolean;
  criteria: Nullable<string>;
  grouper?: {
    id: string;
  };
}

export interface ProcessShow {
  fields: {
    field: {
      name: string;
      group: 'STUDENT' | 'RESPONSIBLE';
      type: string;
    };
    id: string;
    required: boolean;
    weight: number;
  }[];
  id: string;
  name: string;
  periods: {
    id: string;
    name: string;
  }[];
  schoolYear: {
    year: string;
  };
  stages: Stage[];
}

export interface ProcessStage {
  allowWaitingList: boolean;
  endAt: Nullable<string>;
  endHourAt: Nullable<string>;
  id: Nullable<string>;
  observation: Nullable<string>;
  radius: Nullable<number>;
  renewalAtSameSchool: boolean;
  startAt: Nullable<string>;
  startHourAt: Nullable<string>;
  type: string;
  restrictionType: ProcessStageRestriction;
  allowSearch: boolean;
}

export interface Course {
  id: string;
  name: string;
  grades: {
    id: string;
    name: string;
  }[];
}

export interface Field {
  priority: boolean;
  open: boolean;
  checked: string | number | boolean;
  weight: number;
  order: number;
  id: string;
  field?: ProcessFieldField;
  required: string | number | boolean;
  group?: 'STUDENT' | 'RESPONSIBLE';
  mandatory?: boolean;
  internal?: string;
  name?: string;
  options?: Option[];
  type?: string;
}

export interface Fields {
  field: string;
  order: number;
  required: string | number | boolean;
  weight: number;
}

export interface ProcessGetList {
  processes: Process[];
  years: Option[];
  status: Option[];
}

export interface ProcessPostAction {
  errors?: ErrorResponse[];
  process: ProcessShow;
}

export interface ProcessRejectInBatch {
  id: string;
  name: string;
  totalWaitingPreRegistrations: number;
}

export interface ProcessRejectInBatchResponse {
  errors?: ErrorResponse[];
  rejectInBatch: number;
}

export interface ProcessStageResponse {
  process: {
    id: string;
    name: string;
  };
  stages: ProcessStage[];
}

export interface ProcessPostStage {
  id: Nullable<string>;
  type: string;
  name: string;
  radius: number;
  startAt: string;
  endAt: string;
  observation: Nullable<string>;
  renewalAtSameSchool: string;
  allowWaitingList: string;
  restrictionType: number;
  allowSearch: boolean;
}

export interface ProcessFieldResponse {
  id: string;
  name: string;
  fields: {
    field: {
      id: string;
    };
    order: number;
    required: boolean;
    weight: number;
  }[];
}

export interface ProcessFieldList {
  process: ProcessFieldResponse;
  responsibleFields: Field[];
  studentFields: Field[];
}

export interface ProcessCreateCourses extends IdName {
  grades: {
    data: IdName[];
  };
}

export interface ProcessCreateCoursesResponse extends IdName {
  grades: IdName[];
}

export interface ProcessCreateListCourses {
  courses: ProcessCreateCoursesResponse[];
  periods: Period[];
  schoolYears: Option[];
  schools: Option[];
  groupers: Option[];
}

export interface ProcessGrouper {
  id: string;
  name: string;
  waitingListLimit: number;
  processes: Process[];
}
