import { ErrorResponse, FieldPage } from '@/modules/fields/types';
import { graphql } from '@/api';

export const list = (): Promise<FieldPage[]> => {
  const payload = {
    query: `
      query fields {
        fields(first: 100) {
          data {
            id
            name
            description
            internal
            required
            group
            type
            options {
              id
              name
              weight
            }
          }
        }
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        fields: {
          data: FieldPage[]
        };
      };
    };
  }>(payload)
    .then(res => res.data.data.fields.data);
};

export const post = (data: FieldPage) => {
  const payload = {
    variables: data,
    query: `
      mutation createField(
        $type: FieldType!
        $group: GroupType!
        $name: String!
        $description: String
        $options: [FieldOptionInput!]
      ) {
        resource: createField(
          input: {
            type: $type
            group: $group
            name: $name
            description: $description
            options: $options
          }
        ) {
          id
          name
        }
      }
    `,
  };

  return graphql<{
    data: {
      errors?: ErrorResponse;
    };
  }>(payload)
    .then(res => res.data);
};

export const put = (data: FieldPage) => {
  const payload = {
    variables: data,
    query: `
      mutation updateField(
        $id: ID!
        $type: FieldType
        $group: GroupType
        $name: String
        $description: String
        $options: [FieldOptionInput!]
        $deleteOptions: [ID!]
      ) {
        resource: updateField(
          input: {
            id: $id
            type: $type
            group: $group
            name: $name
            description: $description
            options: $options
            deleteOptions: $deleteOptions
          }
        ) {
          id
          name
        }
      }
    `,
  };

  return graphql<{
    data: {
      errors?: ErrorResponse;
    };
  }>(payload)
    .then(res => res.data);
};
