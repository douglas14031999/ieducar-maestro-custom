import { Field, Fields, ProcessFieldList, ProcessFieldResponse } from '@/modules/processes/types';
import { ID } from '@/types';
import { ProcessField } from '@/modules/preregistration/types';
import { graphql } from '@/modules/processes/api';

export const list = (data: ID): Promise<ProcessFieldList> => {
  const payload = {
    query: `
      query ProcessFields($id: ID!) {
        process(id: $id) {
          id
          name
          fields {
            field {
              id
            }
            required
            weight
            order
          }
        }
        fields(first: 100) {
          data {
            id
            name
            type
            group
            required
            mandatory: required
            internal
            options {
              id
              name
            }
          }
        }
      }
    `,
    variables: data,
  };

  return graphql<{
    data: {
      data: {
        fields: {
          data: ProcessField[];
        };
        process: ProcessFieldResponse;
      };
    };
  }>(payload)
    .then(res => {
      const allFields: Field[] = res.data.data.fields.data.map((f) => {
        const internalField = res.data.data.process.fields.find((pf) => pf.field.id === f.id);
        const field = {
          ...f,
          priority: false,
          open: false,
          checked: f.required,
          weight: 0,
          order: 99,
        };

        if (internalField) {
          field.checked = true;
          field.weight = internalField.weight;
          field.required = internalField.required;
          field.order = internalField.order;
          field.priority = internalField.weight !== 0;
        }

        return field;
      });

      const responsibleFields = allFields.filter((f) => f.group === 'RESPONSIBLE').sortBy('order');
      const studentFields = allFields.filter((f) => f.group === 'STUDENT').sortBy('order');

      return {
        process: res.data.data.process,
        responsibleFields,
        studentFields,
      }
    });
};

export const post = (data: {
  id: string;
  fields: Fields[];
}) => {
  const payload = {
    variables: data,
    query: `
      mutation saveProcessFields(
        $id: ID!
        $fields: [ProcessFieldInput!]!
      ) {
        process: saveProcessFields(
          id: $id
          fields: $fields
        ) {
          id
        }
      }
    `,
  };

  return graphql<{
    data: {
      data: {
        process: {
          id: string;
          fields: Fields[];
        }
      }
    }
  }>(payload).then(res => res.data.data.process);
};
