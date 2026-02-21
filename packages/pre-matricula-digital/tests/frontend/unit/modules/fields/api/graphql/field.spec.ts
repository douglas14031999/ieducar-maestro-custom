import { flushPromises } from '@vue/test-utils';
import { Field as FieldApi, graphql } from '@/modules/fields/api';
import { expect, vi } from 'vitest';
import { ErrorResponse, FieldPage, FieldPageOption } from '@/modules/fields/types';

vi.mock('@/api');

describe('@/modules/fields/api/services/graphql/field', () => {
  test('should list successfully fields', async () => {
    const mockedFieldPageOption: FieldPageOption[] = [
      {
        id: 'string',
        name: 'string',
        weight: 300,
      }
    ];

    const mockedFieldPage: FieldPage[] = [
      {
        id: 'string',
        name: 'string',
        group: 'RESPONSIBLE',
        type: 'string',
        internal: false,
        options: mockedFieldPageOption,
        required: false,
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          fields: {
            data: mockedFieldPage
          }
        }
      }
    });

    await flushPromises();

    const response = await FieldApi.list();

    expect(response).toStrictEqual(mockedFieldPage);
  });

  test('should post successfully', async () => {
    const mockedFieldPageOption: FieldPageOption[] = [{
      id: 'string',
      name: 'string',
      weight: 300,
    }]

    const mockedFieldPage: FieldPage = {
      id: 'string',
      name: 'string',
      group: 'RESPONSIBLE',
      type: 'string',
      internal: false,
      options: mockedFieldPageOption,
      required: false,
    };

    const errorsMock: ErrorResponse = {
      message: '',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
      }
    });

    await flushPromises();

    const response = await FieldApi.post(mockedFieldPage);

    expect(response).toStrictEqual({
      errors: errorsMock,
    });
  });

  test('should post without successfully', async () => {
    const mockedFieldPageOption: FieldPageOption[] = [{
      id: 'string',
      name: 'string',
      weight: 300,
    }]

    const mockedFieldPage: FieldPage = {
      id: 'string',
      name: 'string',
      group: 'RESPONSIBLE',
      type: 'string',
      internal: false,
      options: mockedFieldPageOption,
      required: false,
    };

    const errorsMock: ErrorResponse = {
      message: 'algo inesperado aconteceu',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
      }
    });

    await flushPromises();

    const response = await FieldApi.post(mockedFieldPage);

    expect(response).toStrictEqual({
      errors: errorsMock,
    });
  });

  test('should put successfully', async () => {
    const mockedFieldPageOption: FieldPageOption[] = [{
      id: 'string',
      name: 'string',
      weight: 300,
    }]

    const mockedFieldPage: FieldPage = {
      id: 'string',
      name: 'string',
      group: 'RESPONSIBLE',
      type: 'string',
      internal: false,
      options: mockedFieldPageOption,
      required: false,
    };

    const errorsMock: ErrorResponse = {
      message: '',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
      }
    });

    await flushPromises();

    const response = await FieldApi.put(mockedFieldPage);

    expect(response).toStrictEqual({
      errors: errorsMock,
    });
  });

  test('should put without successfully', async () => {
    const mockedFieldPageOption: FieldPageOption[] = [{
      id: 'string',
      name: 'string',
      weight: 300,
    }]

    const mockedFieldPage: FieldPage = {
      id: 'string',
      name: 'string',
      group: 'RESPONSIBLE',
      type: 'string',
      internal: false,
      options: mockedFieldPageOption,
      required: false,
    };

    const errorsMock: ErrorResponse = {
      message: 'algo inesperado aconteceu',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
      }
    });

    await flushPromises();

    const response = await FieldApi.put(mockedFieldPage);

    expect(response).toStrictEqual({
      errors: errorsMock,
    });
  });
  
});