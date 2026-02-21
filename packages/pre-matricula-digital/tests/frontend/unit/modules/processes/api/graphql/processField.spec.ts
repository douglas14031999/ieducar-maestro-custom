import '@/plugin/sortby';
import { flushPromises } from '@vue/test-utils';
import { ProcessField as ProcessApi, graphql } from '@/modules/processes/api';
import { expect, vi } from 'vitest';
import { ProcessField, ProcessFieldField } from '@/modules/preregistration/types';
import { Field, Fields, ProcessFieldResponse } from '@/modules/processes/types';
import { ID } from '@/types';

vi.mock('@/api');

describe('@/modules/processes/api/services/graphql/process', () => {
  test('should list successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const processFieldFieldMocked: ProcessFieldField = {
      id: 'string',
      name: 'string',
      group: 'string',
      internal: 'student_rg',
      type: 'CPF',
      options: [
        {
          id: 'string',
          key: 'string',
          name: 'string',
          label: 'string',
        }
      ],
    };

    const processFieldMocked: ProcessField[] = [
      {
        id: 'string',
        order: 22,
        field: processFieldFieldMocked,
        required: 'string' ,
        weight: 222,
      }
    ];

    const processFieldResponseMocked:  ProcessFieldResponse = {
      id: 'string',
      name: 'string',
      fields: [
        {
          field: {
            id: 'string',
          },
          order: 1,
          required: false,
          weight: 2,
        },
      ]
    }

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          fields: {
            data: processFieldMocked,
          },
          process: processFieldResponseMocked,
        },
      },
    });

    await flushPromises();

    const response = await ProcessApi.list(processIdMock);

    const allFields: Field[] = processFieldMocked.map((f) => {
      const internalField = processFieldResponseMocked.fields.find((pf) => pf.field.id === f.id);
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

    expect(response).toStrictEqual({
      process: processFieldResponseMocked,
      responsibleFields,
      studentFields,
    });
  });

  test('should post successfully', async () => {
    const fieldsMocked: Fields[] = [
      {
        field: 'string',
        order: 2,
        required: 'string',
        weight: 2,
      },
    ];

    const processParamsMock =  {
      id: 'string',
      fields: fieldsMocked,
    }

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          process: processParamsMock
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.post(processParamsMock);

    expect(response).toStrictEqual(processParamsMock);
  });
});
