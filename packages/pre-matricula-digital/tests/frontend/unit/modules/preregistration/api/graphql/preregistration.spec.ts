import { flushPromises } from '@vue/test-utils';
import { Preregistration, graphql } from '@/modules/preregistration/api';
import { expect, vi } from 'vitest';
import { Address, Student } from '@/types';
import {
  PreregistrationBatchResponse,
  PreregistrationClassroom,
  PreRegistrationStage, PreRegistrationStageProcess,
  PreregistrationSubmitInput
} from '@/modules/preregistration/types';
import {ProcessStageRestriction, ProcessType, Stage} from "@/modules/processes/types";

vi.mock('@/api');

describe('@/modules/preregistration/api/services/graphql/preregistration', () => {
  test('should list successfully preregistrations', async () => {
    const mocked = {
      stage: {
        id: 'string',
        renewalAtSameSchool: false,
        allowWaitingList: false,
        radius: 1,
        type: 'string',
        status: 'string',
        process: {},
      },
      preregistration: {},
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: mocked,
      }
    });

    await flushPromises();

    const response = await Preregistration.list({
      id: '1'
    });

    expect(response).toStrictEqual(mocked);
  });

  test('should postMatch successfully', async () => {
    const matchMock = {
      id: 1,
      grade: 'string',
      period: 'string',
      school: 'string',
      initials: 'string',
      dateOfBirth: 'string',
    };

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

    const stageMock: PreRegistrationStage = {
      id: 'string',
      renewalAtSameSchool: false,
      allowWaitingList: false,
      radius: 0,
      type: 'string',
      status: 'string',
      restrictionType: 'NONE',
      process: {} as PreRegistrationStageProcess,
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          matches: [matchMock]
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postMatch({
      stage: stageMock,
      student: studentProtocolMock,
    });

    expect(response).toStrictEqual([matchMock]);
  });

  test('should postMatch without success', async () => {
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

    const stageMock: PreRegistrationStage = {
      id: 'string',
      renewalAtSameSchool: false,
      allowWaitingList: false,
      radius: 0,
      type: 'string',
      status: 'string',
      restrictionType: 'NONE',
      process: {} as PreRegistrationStageProcess,
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          matches: []
        },
      }
    });

    await flushPromises();

    const response = await Preregistration.postMatch({
      stage: stageMock,
      student: studentProtocolMock,
    });

    expect(response).toStrictEqual([]);
  });

  test('should post successfully', async () => {
    const addressMock: Address = {
      postalCode: 'string',
      address: 'string',
      number: 'string',
      complement: 'string',
      neighborhood: 'string',
      city: 'string',
      stateAbbreviation: 'string',
      cityIbgeCode: 0,
      lat: 0,
      lng: 0,
      manualChangeLocation: false,
    };

    const preregistrationSubmitMock: PreregistrationSubmitInput = {
      process: '1',
      stage: 'string',
      type: 'string',
      grade: 'string',
      period: 'string',
      school: 'string',
      optionalSchool: 'string',
      optionalPeriod: 'string',
      relationType: 'string',
      address: addressMock,
      externalPerson: 0,
      optionalAddress: addressMock,
      responsible: [{
        field: 'string',
        value: 'string',
      }],
      student: [{
        field: 'string',
        value: 'string',
      }],
    };

    const preregistrationOverviewMock = {
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

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          preregistrations: [preregistrationOverviewMock]
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.post(preregistrationSubmitMock);

    expect(response).toStrictEqual({
      errors: undefined,
      preregistrations: [preregistrationOverviewMock]
    });
  });

  test('should post without success', async () => {
    const addressMock: Address = {
      postalCode: 'string',
      address: 'string',
      number: 'string',
      complement: 'string',
      neighborhood: 'string',
      city: 'string',
      stateAbbreviation: 'string',
      cityIbgeCode: 0,
      lat: 0,
      lng: 0,
      manualChangeLocation: false,
    };

    const preregistrationSubmitMock: PreregistrationSubmitInput = {
      process: '1',
      stage: 'string',
      type: 'string',
      grade: 'string',
      period: 'string',
      school: 'string',
      optionalSchool: 'string',
      optionalPeriod: 'string',
      relationType: 'string',
      address: addressMock,
      externalPerson: 0,
      optionalAddress: addressMock,
      responsible: [{
        field: 'string',
        value: 'string',
      }],
      student: [{
        field: 'string',
        value: 'string',
      }],
    };

    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          preregistrations: []
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.post(preregistrationSubmitMock);

    expect(response).toStrictEqual({
      errors: errorsMock,
      preregistrations: []
    });
  });

  test('should postAcceptBatch successfully', async () => {
    const preregistrationBatchMock: PreregistrationBatchResponse[] = [
      {
        id: '1',
        student: {
          name: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          acceptPreRegistrations: preregistrationBatchMock
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postAcceptBatch({
      ids: [1],
      classroom: 1,
    });

    expect(response).toStrictEqual({
      errors: undefined,
      acceptPreRegistrations: preregistrationBatchMock
    });
  });

  test('should postAcceptBatch without success', async () => {
    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          acceptPreRegistrations: []
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postAcceptBatch({
      ids: [1],
      classroom: 1,
    });

    expect(response).toStrictEqual({
      errors: errorsMock,
      acceptPreRegistrations: []
    });
  });

  test('should postRejectBatch successfully', async () => {
    const preregistrationBatchMock: PreregistrationBatchResponse[] = [
      {
        id: '1',
        student: {
          name: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          rejectPreRegistrations: preregistrationBatchMock
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postRejectBatch({
      ids: ['1'],
      justification: 'string',
    });

    expect(response).toStrictEqual({
      errors: undefined,
      rejectPreRegistrations: preregistrationBatchMock
    });
  });

  test('should postRejectBatch without success', async () => {
    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          rejectPreRegistrations: []
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postRejectBatch({
      ids: ['1'],
      justification: 'string',
    });

    expect(response).toStrictEqual({
      errors: errorsMock,
      rejectPreRegistrations: []
    });
  });

  test('should postSummonBatch successfully', async () => {
    const preregistrationBatchMock: PreregistrationBatchResponse[] = [
      {
        id: '1',
        student: {
          name: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          summonPreRegistrations: preregistrationBatchMock
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postSummonBatch({
      ids: ['1'],
      justification: 'string',
    });

    expect(response).toStrictEqual({
      errors: undefined,
      summonPreRegistrations: preregistrationBatchMock
    });
  });

  test('should postSummonBatch without success', async () => {
    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          summonPreRegistrations: []
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postSummonBatch({
      ids: ['1'],
      justification: 'string',
    });

    expect(response).toStrictEqual({
      errors: errorsMock,
      summonPreRegistrations: []
    });
  });
  
  test('should getClassroomsByPreregistration successfully', async () => {
    const preregistrationClassroomMock: PreregistrationClassroom[] = [
      {
        key: 1,
        label: 'string',
        period: {
          name: 'string',
        },
        available: 1,
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          classrooms: {
            data: preregistrationClassroomMock
          }
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.getClassroomsByPreregistration({
      period: 'string',
      school: 'string',
      grade: 'string',
      year: 2021,
    });

    expect(response).toStrictEqual([{
      key: 1,
      label: 'string/string (vagas: 1)',
    }]);
  });
  
  test('should postSendEmail successfully', async () => {
    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          success: true
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postSendEmail({
      preregistrations: [1],
      email: 'email@example.com'
    });

    expect(response).toStrictEqual(true);
  });
  
  test('should postSendEmail without success', async () => {
    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          success: false
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.postSendEmail({
      preregistrations: [1],
      email: 'email@example.com'
    });

    expect(response).toStrictEqual(false);
  });

  test('should getList successfully', async () => {
    const filterMock = {
      first: 1,
      search: 'string',
      process: 'string',
      school: 'string',
      period: 'string',
      grade: 'string',
      type: 'string',
      status: 'string',
      sort: 'string',
      canAcceptInBatch: true,
      year: 2020,
      schools: ['string'],
    };

    const preregistrationListMock = [{
      id: 'string',
      type: 'string',
      protocol: 'string',
      status: 'string',
      student: {
        name: 'string',
      },
      grade: {
        name: 'string',
      },
      period: {
        name: 'string',
      },
      school: {
        name: 'string',
      },
      schoolYear: {
        year: 'string',
      },
      position: 1,
      waiting: {
        id: 'string',
        type: 'string',
        status: 'string',
        position: 1,
        student: {
          name: 'string',
        },
        period: {
          name: 'string',
        },
        school: {
          name: 'string',
        },
        schoolYear: {
          year: 'string',
        },
      },
      parent: {
        id: 'string',
        type: 'string',
        status: 'string',
        position: 1,
        student: {
          name: 'string',
        },
        period: {
          name: 'string',
        },
        school: {
          name: 'string',
        },
        schoolYear: {
          year: 'string',
        },
      },
    }];

    const paginatorInfoMock  = {
      count: 0,
      currentPage: 0,
      lastPage: 0,
      perPage: 0,
      total: 0,
    };

    const statProcessesMock = [{
      id: 'string',
      accepted: 1,
      rejected: 1,
      total: 1,
      vacancies: 1,
      waiting: 1,
    }];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          preregistrations: {
            data: preregistrationListMock,
            paginatorInfo: paginatorInfoMock,
          },
          processes: {
            data: statProcessesMock,
          },
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.getList(filterMock);

    expect(response).toStrictEqual({
      errors: false,
      stats: {
        accepted: 1,
        rejected: 1,
        total: 1,
        vacancies: 1,
        waiting: 1,
      },
      preregistrations: preregistrationListMock,
      paginator: paginatorInfoMock,
    });
  });

  test('should getList without success', async () => {
    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    const filterMock = {
      first: 1,
      search: 'string',
      process: 'string',
      school: 'string',
      period: 'string',
      grade: 'string',
      type: 'string',
      status: 'string',
      sort: 'string',
      canAcceptInBatch: true,
      year: 2020,
      schools: ['string'],
    };

    const paginatorInfoMock  = {
      count: 0,
      currentPage: 0,
      lastPage: 0,
      perPage: 0,
      total: 0,
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          preregistrations: {
            data: [],
            paginatorInfo: paginatorInfoMock,
          },
          processes: {
            data: [],
          },
        },
        errors: errorsMock
      }
    });

    await flushPromises();

    const response = await Preregistration.getList(filterMock);

    expect(response).toStrictEqual({
      errors: true,
      stats: {
        accepted: 0,
        rejected: 0,
        total: 0,
        vacancies: 0,
        waiting: 0,
      },
      preregistrations: [],
      paginator: paginatorInfoMock,
    });
  });
  
  test('should getPreregistrations successfully', async () => {
    const gradesMock = [
      {
        id: '1',
        key: 'string',
        label: 'string',
        name: 'string',
      }
    ];

    const schoolsMock = [
      {
        id: '1',
        key: 'string',
        label: 'string',
        name: 'string',
      }
    ];

    const processesMock = [
      {
        id: '1',
        key: 'string',
        label: 'string',
        name: 'string',
        schoolYear: {
          year: 2020
        },
        totalPreRegistrations: 1,
      }
    ];

    const periodsMock = [
      {
        id: '1',
        name: 'string',
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          grades: {
            data: gradesMock,
          },
          periods: {
            data: periodsMock,
          },
          processes: {
            data: processesMock,
          },
          schools: {
            data: schoolsMock,
          }
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.getPreregistrations({
      schools: ['1'],
    });

    expect(response).toStrictEqual({
      grades: gradesMock,
      periods: periodsMock,
      processes: processesMock,
      schools: schoolsMock,
    });
  });
    
  test('should postAccept successfully', async () => {
    vi.mocked(graphql).mockResolvedValueOnce({
      data: {}
    });

    await flushPromises();

    const response = await Preregistration.postAccept({
      ids: [1],
      classroom: 1
    });

    expect(response).toStrictEqual(undefined);
  });
  
  test('should postAccept without success', async () => {
    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock
      }
    });

    await flushPromises();

    const response = await Preregistration.postAccept({
      ids: [1],
      classroom: 1
    });

    expect(response).toStrictEqual(errorsMock);
  });
    
  test('should postReject successfully', async () => {
    vi.mocked(graphql).mockResolvedValueOnce({
      data: {}
    });

    await flushPromises();

    const response = await Preregistration.postReject({
      ids: [1],
      justification: 'string'
    });

    expect(response).toStrictEqual({
      data: {}
    });
  });
  
  test('should postReject without success', async () => {
    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock
      }
    });

    await flushPromises();

    const response = await Preregistration.postReject({
      ids: [1],
      justification: 'string'
    });

    expect(response).toStrictEqual({
      data: {
        errors: errorsMock
      }
    });
  });
    
  test('should postReturnToWait successfully', async () => {
    vi.mocked(graphql).mockResolvedValueOnce({
      data: {}
    });

    await flushPromises();

    const response = await Preregistration.postReturnToWait({
      ids: [1],
    });

    expect(response).toStrictEqual({
      data: {}
    });
  });
  
  test('should postReturnToWait without success', async () => {
    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock
      }
    });

    await flushPromises();

    const response = await Preregistration.postReturnToWait({
      ids: [1],
    });

    expect(response).toStrictEqual({
      data: {
        errors: errorsMock
      }
    });
  });

  test('should postSummon successfully', async () => {
    vi.mocked(graphql).mockResolvedValueOnce({
      data: {}
    });

    await flushPromises();

    const response = await Preregistration.postSummon({
      ids: [1],
      justification: 'string'
    });

    expect(response).toStrictEqual(undefined);
  });
  
  test('should postSummon without success', async () => {
    const errorsMock = [
      {
        message: 'string',
        extensions: {
          message: 'string',
        }
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock
      }
    });

    await flushPromises();

    const response = await Preregistration.postSummon({
      ids: [1],
      justification: 'string'
    });

    expect(response).toStrictEqual(errorsMock);
  });
  
  test('should listByProtocol successfully', async () => {
    const preregistrationMock = {
      id: 1,
      date: 'string',
      protocol: 'string',
      status: 'string',
      type: 'string',
      observation: 'string',
      code: 'string',
      grade: {
        id: 'string',
        name: 'string',
        course: {
          name: 'string',
        },
      },
      period: {
        id: 'string',
        name: 'string',
      },
      school: {
        id: 'string',
        name: 'string',
      },
      classroom: {
        id: 'string',
        name: 'string',
      },
      position: 1,
      waiting: {
        id: 'string',
        protocol: 'string',
        school: {
          id: 'string',
          name: 'string',
        },
        position: 1,
      },
      parent: {
        id: 'string',
        protocol: 'string',
        school: {
          id: 'string',
          name: 'string',
        },
        position: 1,
      },
      student: {
        student_name: 'string',
        student_date_of_birth: 'string',
        student_cpf: 'string',
        student_rg: 'string',
        student_marital_status: 0,
        student_place_of_birth: 0,
        student_birth_certificate: 'string',
        student_gender: 0,
        student_email: 'string',
        student_phone: 'string',
        student_mobile: 'string',
      },
      responsible: {
        responsible_name: 'string',
        responsible_date_of_birth: 'string',
        responsible_cpf: 'string',
        responsible_rg: 'string',
        responsible_marital_status: 0,
        responsible_place_of_birth: 0,
        responsible_gender: 0,
        responsible_email: 'string',
        responsible_phone: 'string',
        responsible_mobile: 'string',
        responsible_address: [{
          postalCode: 'string',
          address: 'string',
          number: 'string',
          neighborhood: 'string',
          city: 'string',
          complement: 'string',
          manualChangeLocation: false,
        }],
      },
      relationType: 'string',
      fields: [{
        id: 'string',
        value: 'string',
        field: {
          id: 'string',
          name: 'string',
          internal: 'string',
          group: 'string',
        },
      }],
      process: {
        id: 'string',
        name: 'string',
        fields: [{
          id: '1',
          order: 0,
          field: [
            {
              id: 'string',
              name: 'string',
              group: 'string',
              internal: 'responsible_rg',
              type: 'CPF',
              options: [{
                id: 'string',
                key: 'string',
                name: 'string',
                label: 'string',
              }],
            }
          ],
          required: true,
          weight: 0,
        }],
        schoolYear: {
          year: 2020,
        },
      },
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          preregistration: preregistrationMock,
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.listByProtocol({
      protocol: 'string'
    });

    expect(response).toStrictEqual(preregistrationMock);
  });
  
  test('should listByProtocol without success', async () => {
    const preregistrationMock = undefined;

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          preregistration: preregistrationMock,
        }
      }
    });

    await flushPromises();

    const response = await Preregistration.listByProtocol({
      protocol: 'string'
    });

    expect(response).toStrictEqual(preregistrationMock);
  });
});
