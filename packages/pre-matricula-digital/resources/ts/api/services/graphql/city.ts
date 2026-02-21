import { graphql } from '@/api';

export const list = (data: {
  params: {
    search: string;
  }
}) => {
  const payload = {
    variables: data.params,
    query: `
      query cities(
        $search: String
      ) {
        cities(
          search: $search
          orderBy: [
            {
              column: "name"
              order: ASC
            }
          ]
        ) {
          data {
            key:id
            label:name
            state {
              abbreviation
            }
          }
        }
      }
    `,
  };

  return graphql(payload);
};
