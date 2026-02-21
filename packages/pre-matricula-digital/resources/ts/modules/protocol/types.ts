import { ID, Nullable } from '@/types';
import {
  PreRegistrationOverview,
  PreRegistrationStageProcessGrade,
} from '@/modules/preregistration/types';

export interface FindProtocolPreRegistration {
  id: number;
  protocol: string;
  status: string;
  process: {
    schoolYear: {
      year: number;
    };
  };
  student: {
    initials: string;
    dateOfBirth: string;
  };
}

export interface ProtocolStatusPreRegistration {
  classroom: {
    grade: {
      name: string;
    };
    name: string;
    period: {
      name: string;
    };
  };
  id: string;
  observation: string;
  parent: Nullable<PreRegistrationOverview>;
  position?: number;
  process: {
    showPriorityProtocol: boolean;
    blockIncompatibleAgeGroup: boolean;
    forceSuggestedGrade: boolean;
    grades: PreRegistrationStageProcessGrade[];
  };
  school: {
    area_code: number;
    name: string;
    phone: string;
  };
  stage: {
    observation: string;
  };
  status: 'ACCEPTED' | 'WAITING' | 'REJECTED' | 'SUMMONED' | 'IN_CONFIRMATION';
  student: {
    dateOfBirth: string;
    initials: string;
  };
  type: string;
  waiting: Nullable<PreRegistrationOverview>;
}

export interface ProtocolStatusReturnToWait {
  errors: boolean;
  keepOnTheWaitingList: ID;
}
