import {ErrorResponse, ID} from '@/types';
import {ProcessGrouper} from '@/modules/processes/types';
import { graphql } from '@/modules/processes/api';

interface ProcessesGrouperListResponse {
  data: {
    data: {
      groupers: ProcessGrouper[];
    };
  };
}

interface ProcessGrouperCreate {
  name: string;
  waitingListLimit: number;
  processes: ID[];
}

export const list = (): Promise<ProcessGrouper[]> => {
  const payload = {
    query: `
      query groupers {
        groupers {
          id
          name
          waitingListLimit
          processes {
            id
            name
          }
        }
      }
    `,
  };

  return graphql<ProcessesGrouperListResponse>(payload).then(res => res.data.data.groupers);
};

export const remove = (data: ID): Promise<{
  errors?: ErrorResponse[];
  deleteProcessGrouper: ProcessGrouper,
}> => {
  const payload = {
    variables: data,
    query: `
      mutation deleteProcessGrouper(
        $id: ID!
      ) {
        deleteProcessGrouper(
          id: $id
        ) {
          name
        }
      }
    `,
  };

  return graphql<{
    data: {
      errors: ErrorResponse[];
      data: {
        deleteProcessGrouper: ProcessGrouper;
      };
    };
  }>(payload)
    .then(res => ({
      errors: res.data.errors,
      deleteProcessGrouper: res.data.data.deleteProcessGrouper,
    }));
};

export const post = (data: ProcessGrouperCreate): Promise<ProcessGrouper> => {
  const payload = {
    variables: {
      input: data
    },
    query: `
      mutation saveProcessGrouper(
        $input: ProcessGrouperInput!
      ) {
        saveProcessGrouper(
          input: $input
        ) {
          name
          waitingListLimit
          processes {
            id
            name
          }
        }
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        saveProcessGrouper: ProcessGrouper
      }
    }
  }>(payload)
    .then(res => res.data.data.saveProcessGrouper);
};
