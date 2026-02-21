import { ErrorResponse, ID, Nullable, Student } from '@/types';
import { FindProtocolPreRegistration, ProtocolStatusPreRegistration, ProtocolStatusReturnToWait } from '@/modules/protocol/types';
import { defineFindProtocolQueryAndVariables } from '@/util';
import { graphql } from '@/modules/protocol/api';

export const show = (data: {
  search: string;
}): Promise<Nullable<ProtocolStatusPreRegistration>> => {
  const payload = {
    variables: data,
    query: `
      query preregistration(
        $search: String
      ) {
        preregistration: preregistrationByProtocol(protocol: $search) {
          id
          type
          status
          position
          student {
            initials
            dateOfBirth
          }
          school {
            name
            area_code
            phone
          }
          classroom {
            name
            period {
              name
            }
            grade {
              name
            }
          }
          observation
          waiting {
            position
            protocol
            school {
              name
              area_code
              phone
            }
            process {
              showPriorityProtocol
            }
          }
          parent {
            position
            protocol
            school {
              name
              area_code
              phone
            }
            process {
              showPriorityProtocol
            }
          }
          stage {
            observation
          }
          process {
            showPriorityProtocol
            forceSuggestedGrade
            blockIncompatibleAgeGroup
            grades {
              id
              name
              startBirth
              endBirth
            }
          }
        }
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        preregistration: ProtocolStatusPreRegistration;
      }
    }
  }>(payload)
    .then(res => res.data.data.preregistration);
};

export const postReturnToWait = (data: ID, grade: Nullable<string>): Promise<ProtocolStatusReturnToWait> => {
  const payload = {
    variables: {
      ...data,
      grade
    },
    query: `
      mutation keepOnTheWaitingList(
        $id: ID!
        $grade: ID
      ) {
        keepOnTheWaitingList(
          id: $id
          grade: $grade
        )
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        keepOnTheWaitingList: ID;
      };
      errors?: ErrorResponse;
    }
  }>(payload)
    .then(res => ({
      errors: Boolean(res.data.errors),
      keepOnTheWaitingList: res.data.data.keepOnTheWaitingList,
    }));
};

export const postFindProtocol = (data: {
  type: string;
  studentModel: Student;
}): Promise<FindProtocolPreRegistration[]> => {
  const payload = defineFindProtocolQueryAndVariables(
    data.type,
    data.studentModel,
  );

  return graphql<{
    data: {
      data: {
        preregistrations: FindProtocolPreRegistration[];
      }
    }
  }>(payload)
    .then(res => res.data.data.preregistrations);
};
