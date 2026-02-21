import { ErrorResponse, ID } from '@/types';
import { Notice, NoticeSubmitResponse } from '@/modules/notice/types';
import { graphql } from '@/modules/notice/api';

export const list = (): Promise<Notice> => {
  const payload = {
    query: `
      query{
        notices {
          data {
            id
            text
          }
        }
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        notices: {
          data: Notice[]
        }
      }
    }
  }>(payload)
    .then(res => res.data.data.notices.data[0]);
};

export const post = (data: Notice): Promise<NoticeSubmitResponse> => {
  const payload = {
    query: `
      mutation save(
        $input: NoticeInput!
      ) {
        notice: saveNotice(
          input: $input
        ) {
          id
        }
      }
    `,
    variables: {
      input: data
    },
  };

  return graphql<{
    data: {
      errors?: ErrorResponse;
      data: {
        notice: {
          id: number;
        };
      };
    }
  }>(payload)
    .then(res => ({
      errors: Boolean(res.data.errors),
      id: res.data.data.notice.id,
    }));
};

export const remove = (data: ID<number>): Promise<NoticeSubmitResponse> => {
  const payload = {
    query: `
      mutation delete(
        $id: ID!
      ) {
        notice: deleteNotice(
          id: $id
        ) {
          id
        }
      }
    `,
    variables: data,
  };

  return graphql<{
    data: {
      errors?: ErrorResponse
    }
  }>(payload)
    .then(res => ({
      errors: Boolean(res.data.errors),
    }));
};
