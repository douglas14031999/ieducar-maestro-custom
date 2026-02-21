import '@/plugin/sortby';
import { stageStatusText } from '@/util';
import { getDate, getFormattedTime } from '@/datetime';
import { flushPromises } from '@vue/test-utils';
import { Process as ProcessApi, graphql } from '@/modules/processes/api';
import { expect, vi } from 'vitest';
import { Grades, Processes, SchoolYear, Stages, ID, ErrorResponse, IdName, Option } from '@/types';
import { Period, Process, ProcessCheck, ProcessCreate, ProcessCreateCourses, ProcessCreateResponse, ProcessPostStage, ProcessShow, ProcessStage, ProcessType, Stage } from '@/modules/processes/types';
import { ProcessField, ProcessFieldField } from '@/modules/preregistration/types';

vi.mock('@/api');

describe('@/modules/processes/api/services/graphql/process', () => {
  test('should list successfully', async () => {
    const gradesMocked: Grades[] = [
      {
        id: 1,
        name: 'string',
        label: 'string',
        key: 2,
      }
    ];

    const stagesMocked: Stages[] = [
      {
        endAt: 'string',
        id: 'string',
        name: 'string',
        startAt: 'string',
        status: 'string',
        type: 'string',
      }
    ];

    const schoolYearMoked: SchoolYear = {
      year: 2022,
    }

    const processesMoked: Processes[] = [
      {
        id: 'string',
        name: 'string',
        schoolYear: schoolYearMoked,
        stages: stagesMocked,
        grades: gradesMocked,
        startAt: 'string',
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          processes: {
            data: processesMoked,
          }
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.list();

    expect(response).toStrictEqual(processesMoked);
  });

  test('should remove with successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const stageMocked: Stage[] = [
      {
        endAt: 'string',
        id: 'string',
        name: 'string',
        startAt: 'string',
        type: 'REGISTRATION_RENEWAL',
        status: 'string',
        observation: 'string',
        totalWaitingPreRegistrations: 10,
      }
    ];

   const processShowMocked: ProcessShow = {
      fields: [
        {
          field: {
            name: 'string',
            group: 'STUDENT',
            type: 'string',
          },
          id: 'string',
          required: false,
          weight: 200,
        }
      ],
      id: 'string',
      name: 'string',
      periods: [
        {
          id: 'string',
          name: 'string',
        }
      ],
      schoolYear: {
        year: 'string',
      },
      stages: stageMocked,
    }

    const errorsMock: ErrorResponse = {
      message: '',
      extensions: {
        message: '',
      }
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          process: processShowMocked
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.remove(processIdMock);

    expect(response).toStrictEqual({
      errors: errorsMock,
      process: processShowMocked,
    });
  });

  test('should remove without successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const errorsMock: ErrorResponse = {
      message: 'Algo deu errado.',
      extensions: {
        message: 'Entre em contato com o suporte.',
      }
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          process: []
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.remove(processIdMock);

    expect(response).toStrictEqual({
      errors: errorsMock,
      process: [],
    });
  });

  test('should show with successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const processTypeMocked: ProcessType = 'REGISTRATION_RENEWAL';

    const stageMocked: Stage[] = [
      {
        endAt: 'string',
        id: 'string',
        name: 'string',
        startAt: 'string',
        type: processTypeMocked,
        status: 'string',
        observation: 'string',
        totalWaitingPreRegistrations: 10,
      }
    ];

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

    const periodMocked: Period[] = [
      {
        id: 'string',
        name: 'string',
      }
    ];

    const schoolYearMoked: SchoolYear = {
      year: 2022,
    }

    const processCheckMocked: ProcessCheck = {
      id: '1',
      name: 'string',
      fields: processFieldMocked,
        grades: [
        {
          endBirth: 'string',
          id: 'string',
          name: 'string',
          startBirth: 'string',
        },
      ],
      periods: periodMocked,
      schoolYear: schoolYearMoked,
      schools: [
        {
          id: 'string',
          latitude: 22,
          longitude: 33,
          name: 'string',
        },
      ],
      stages: stageMocked,
      vacancies: [
        {
          available: 33,
          grade: 'string',
          period: 'string',
          school: 'string',
          total: 20,
        },
      ],
    }

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          process: processCheckMocked
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.show(processIdMock);

    expect(response).toStrictEqual(processCheckMocked);
  });

  test('should post successfully', async () => {
    const processCreateMocked: ProcessCreate = {
      active: true,
      forceSuggestedGrade: false,
      gradeAgeRangeLink: 'string',
      grades: ['string'],
      id: 'string',
      messageFooter: 'string',
      name: 'string',
      periods: ['string'],
      schoolYear: 'string',
      showPriorityProtocol: true,
      allowResponsibleSelectMapAddress: true,
      blockIncompatibleAgeGroup: true,
      selectedSchools: false,
      schools: ['string'],
      waitingListLimit: 1,
      onePerYear: false,
    }

    const processIdMock: ID = {
      id: '1',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          process: processIdMock
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.post(processCreateMocked);

    expect(response).toStrictEqual(processIdMock);
  });

  test('should postStages successfully', async () => {
    const processPostStageMocked: ProcessPostStage[] = [
      {
        id: 'string',
        type: 'string',
        name: 'string',
        radius: 22,
        startAt: 'string',
        endAt: 'string',
        observation: 'string',
        renewalAtSameSchool: 'string',
        allowWaitingList: 'string',
      }
    ];

    const processCreateMocked = {
      id: 'string',
      stages: processPostStageMocked,
    }

    const processIdMock: ID = {
      id: '1',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          process: processIdMock
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.postStages(processCreateMocked);

    expect(response).toStrictEqual(processIdMock);
  });

  test('should postCopy successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const stageMocked: Stage[] = [
      {
        endAt: 'string',
        id: 'string',
        name: 'string',
        startAt: 'string',
        type: 'REGISTRATION_RENEWAL',
        status: 'string',
        observation: 'string',
        totalWaitingPreRegistrations: 10,
      }
    ];

   const processShowMocked: ProcessShow = {
      fields: [
        {
          field: {
            name: 'string',
            group: 'STUDENT',
            type: 'string',
          },
          id: 'string',
          required: false,
          weight: 200,
        }
      ],
      id: 'string',
      name: 'string',
      periods: [
        {
          id: 'string',
          name: 'string',
        }
      ],
      schoolYear: {
        year: 'string',
      },
      stages: stageMocked,
    }

    const errorsMock: ErrorResponse = {
      message: '',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          process: processShowMocked,
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.postCopy(processIdMock);

    expect(response).toStrictEqual({
      errors: errorsMock,
      process: processShowMocked,
    });
  });

  test('should postCopy without successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const errorsMock: ErrorResponse = {
      message: 'Algo inesperado aconteceu.',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          process: [],
        },
      }
    });

    await flushPromises();

    const response = await ProcessApi.postCopy(processIdMock);

    expect(response).toStrictEqual({
      errors: errorsMock,
      process: [],
    });
  });

  test('should listCreate successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const processCreateResponseMocked: ProcessCreateResponse = {
      active:true,
      forceSuggestedGrade:true,
      gradeAgeRangeLink: 'string',
      grades: [
        {
          id: '1',
        }
      ],
      id: 'string',
      messageFooter: 'string',
      name: 'string',
      periods: [
        {
          id: '1',
        }
      ],
      schoolYear: {
        id: 'string',
      },
      showPriorityProtocol: true,
      allowResponsibleSelectMapAddress: true,
      autoRejectByDays: true,
      autoRejectDays: null,
      blockIncompatibleAgeGroup: true,
      selectedSchools: false,
      schools: [
        {
          id: '1',
        }
      ],
      waitingListLimit: 1,
      onePerYear: false,
      grouper: undefined,
    }

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          process: processCreateResponseMocked,
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.listCreate(processIdMock);
    const grades = processCreateResponseMocked.grades.map((g) => g.id);
    const periods = processCreateResponseMocked.periods.map((p) => p.id);
    const schools = processCreateResponseMocked.schools.map((p) => p.id);

    expect(response).toStrictEqual({
      ...processCreateResponseMocked,
      grades,
      periods,
      schoolYear: processCreateResponseMocked.schoolYear.id,
      schools,
    });
  });

  test('should listCourses successfully', async () => {
    const idNamemocked: IdName<string>[] = [
      {
        id: '1',
        name: 'string',
      },
    ]

    const processCreateCoursesMocked: ProcessCreateCourses[] = [
      {
        grades: {
          data: idNamemocked,
        },
        name: 'string',
        id: '1',
      },
    ]

    const periodMocked: Period[] = [
      {
        id: 'string',
        name: 'string',
      }
    ];

    const optionMocked: Option[] = [
      {
        key: 'string',
        label: 'string',
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          courses: {
            data: processCreateCoursesMocked,
          },
          periods: {
            data: periodMocked,
          },
          schoolYears: {
            data: optionMocked,
          },
          schools: {
            data: optionMocked,
          },
          groupers: optionMocked,
        } ,
      },
    });

    await flushPromises();

    const response = await ProcessApi.listCourses();

    const courses = response.courses.map(course => ({
      ...course,
      grades: course.grades,
    }));

    expect(response).toStrictEqual({
      courses,
      periods: periodMocked,
      schoolYears: optionMocked,
      schools: optionMocked,
      groupers: optionMocked,
    });
  });

  test('should getList successfully', async () => {
    const stageMocked: Stage[] = [
      {
        endAt: 'string',
        id: 'string',
        name: 'string',
        startAt: 'string',
        type: 'REGISTRATION_RENEWAL',
        status: 'string',
        observation: 'string',
        totalWaitingPreRegistrations: 10,
      }
    ];

    const schoolYearMoked: SchoolYear = {
      year: 2022,
    }

    const processMocked: Process[] = [
      {
        id: 'string',
        name: 'string',
        active: true,
        key: 'string',
        label: 'string',
        schoolYear: schoolYearMoked,
        stages: stageMocked,
      }
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          processes: {
            data: processMocked,
          },
        },
      },
    });

    await flushPromises();

    const response = await ProcessApi.getList();

    const years = processMocked.map(
      (process) => process.schoolYear.year,
    ).filter((v, i, a) => a.indexOf(v) === i)
      .map((year) => ({ key: year, label: year }))
      .sort((a, b) => b.key - a.key) as unknown as Option[];

      const statusArray: string[] = [];

      processMocked.forEach((process) => {
        process.stages.forEach((stage) => {
          statusArray.push(stage.status);
        });
      });

    expect(response).toStrictEqual({
      processes: processMocked.sortBy('name'),
      years,
      status: statusArray.filter((v, i, a) => a.indexOf(v) === i)
      .map((status) => ({ key: status, label: stageStatusText(status).toUpperCase() })).sortBy('label'),
    });
  });

  test('should getShow successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const stageMocked: Stage[] = [
      {
        endAt: 'string',
        id: 'string',
        name: 'string',
        startAt: 'string',
        type: 'REGISTRATION_RENEWAL',
        status: 'string',
        observation: 'string',
        totalWaitingPreRegistrations: 10,
      }
    ];

    const processShowMocked: ProcessShow = {
      fields: [
        {
          field: {
            name: 'string',
            group: 'STUDENT',
            type: 'string',
          },
          id: 'string',
          required: false,
          weight: 200,
        }
      ],
      id: 'string',
      name: 'string',
      periods: [
        {
          id: 'string',
          name: 'string',
        }
      ],
      schoolYear: {
        year: 'string',
      },
      stages: stageMocked,
    }

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          process: processShowMocked,
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.getShow(processIdMock);

    expect(response).toStrictEqual(processShowMocked);
  });

  test('should postRejectInBatch successfully', async () => {
    const processParamsMocked = {
      id: 'string',
      stageId: 'string',
      justification: 'string',
    }

    const errorsMock: ErrorResponse[] = [
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
          rejectInBatch: 1,
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.postRejectInBatch(processParamsMocked);

    expect(response).toStrictEqual({
      errors: errorsMock,
      rejectInBatch: 1,
    });
  });

  test('should postRejectInBatch without successfully', async () => {
    const processParamsMocked = {
      id: 'string',
      stageId: 'string',
      justification: 'string',
    }

    const errorsMock: ErrorResponse[] = [
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
          rejectInBatch: [],
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.postRejectInBatch(processParamsMocked);

    expect(response).toStrictEqual({
      errors: errorsMock,
      rejectInBatch: [],
    });
  });

  test('should listStages successfully', async () => {
    const processIdMock: ID = {
      id: '1',
    };

    const processStage: ProcessStage[] = [
      {
        allowWaitingList: true,
        endAt: 'string',
        endHourAt: 'string',
        id: '1',
        observation: 'string',
        radius: 123,
        renewalAtSameSchool: true,
        startAt: 'string',
        startHourAt: 'string',
        type: 'string',
      },
    ];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          process: {
            id: 'string',
            name: 'string',
            stages: processStage,
          }
        }
      }
    });

    await flushPromises();

    const response = await ProcessApi.listStages(processIdMock);

    const stages = processStage.map((s) => ({
      ...s,
      startAt: getDate(s.startAt as string),
      endAt: getDate(s.endAt as string),
      startHourAt: getFormattedTime(s.startHourAt as string),
      endHourAt: getFormattedTime(s.endHourAt as string),
    }));

    expect(response).toStrictEqual({
      process: {
        id: 'string',
        name: 'string',
      },
      stages: stages,
    });
  });

});
