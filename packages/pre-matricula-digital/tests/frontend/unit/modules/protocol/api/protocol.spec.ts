import { flushPromises } from '@vue/test-utils';
import { Protocol, graphql } from '@/modules/protocol/api';
import { expect, vi } from 'vitest';
import { Student } from '@/types';

vi.mock('@/api');

describe('@/modules/protocol/api/services/graphql/protocol', () => {
  test('should show successfully protocols', async () => {
    const preregistrationOverview = {
      id: 1,
      protocol: 'string',
      code: 'string',
      type: 'string',
      date: 'string',
      position: 1,
      school: {
        id: 1,
        name: 'string',
        area_code: 'string',
        phone: 'string',
      },
      process: {
        showPriorityProtocol: false,
      },
    };

    const preregistration = {
      classroom: {
        grade: {
          name: 'string',
        },
        name: 'string',
        period: {
          name: 'string',
        },
      },
      id: 'string',
      observation: 'string',
      parent: preregistrationOverview,
      position: 1,
      process: {
        showPriorityProtocol: false,
      },
      school: {
        area_code: 1,
        name: 'string',
        phone: 'string',
      },
      stage: {
        observation: 'string',
      },
      status: 'ACCEPTED',
      student: {
        dateOfBirth: 'string',
        initials: 'string',
      },
      type: 'string',
      waiting: preregistrationOverview,
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          preregistration,
        },
      },
    });

    await flushPromises();

    const response = await Protocol.show({
      search: '000AAA',
    });

    expect(response).toStrictEqual(preregistration);
  });

  test('should postReturnToWait successfully protocols', async () => {
    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          keepOnTheWaitingList: {
            id: 1,
          },
        },
      },
    });

    await flushPromises();

    const response = await Protocol.postReturnToWait({
      id: '1',
    });

    expect(response).toStrictEqual({
      errors: false,
      keepOnTheWaitingList: {
        id: 1,
      },
    });
  });

  test('should postReturnToWait without success protocols', async () => {
    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          keepOnTheWaitingList: {
            id: 1,
          },
        },
        errors: {
          message: 'string',
          extensions: {
            message: 'string',
          },
        },
      },
    });

    await flushPromises();

    const response = await Protocol.postReturnToWait({
      id: '1',
    });

    expect(response).toStrictEqual({
      errors: true,
      keepOnTheWaitingList: {
        id: 1,
      },
    });
  });

  test('should postFindProtocol successfully', async () => {
    const studentProtocolMock: Student = {
      id: 'string',
      name: 'string',
      student_cpf: 'string',
      student_rg: 'string',
      student_birth_certificate: 'string',
      student_name: 'string',
      student_date_of_birth: 'string',
      preregistrations: [],
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          preregistrations: [],
        },
      },
    });

    const response = await Protocol.postFindProtocol({
      type: 'CPF',
      studentModel: studentProtocolMock,
    });

    expect(response).toStrictEqual([]);
  });
});
