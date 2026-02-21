import { flushPromises } from '@vue/test-utils';
import { Notice as NoticeApi, graphql } from '@/modules/notice/api';
import { expect, vi } from 'vitest';
import { Notice } from '@/modules/notice/types';
import { ErrorResponse, ID } from '@/types';

vi.mock('@/api');

describe('@/modules/notice/api/services/graphql/notice', () => {
  test('should list successfully notices', async () => {
    const mockedNotice: Notice[] = [{
      id: 1,
      text: 'string',
    }];

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          notices: {
            data: mockedNotice
          }
        }
      }
    });

    await flushPromises();

    const response = await NoticeApi.list();

    expect(response).toStrictEqual(mockedNotice[0]);

  });

  test('should post successfully', async () => {
    const noticeSubmitMock: Notice = {
      id: 1,
      text: 'string',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          notice: {
            id: 1
          }
        }
      }
    });

    await flushPromises();

    const response = await NoticeApi.post(noticeSubmitMock);

    expect(response).toStrictEqual({
      errors: false,
      id: 1,
    });
  });

  test('should post without success', async () => {
    const noticeSubmitMock: Notice = {
      id: 1,
      text: 'string',
    };

    const errorsMock: ErrorResponse = {
      message: 'string',
      extensions: {
        message: 'string',
      }
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          notice: []
        }
      }
    });

    await flushPromises();

    const response = await NoticeApi.post(noticeSubmitMock);

    expect(response).toStrictEqual({
      errors: true,
      id: undefined,
    });
  });

  test('should remove with successfully', async () => {
    const noticeSubmitMock: ID<number> = {
      id: 1,
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          notice: {
            id: 1
          }
        }
      }
    });

    await flushPromises();

    const response = await NoticeApi.remove(noticeSubmitMock);

    expect(response).toStrictEqual({
      errors: false,
    });
  });

  test('should remove with successfully', async () => {
    const noticeSubmitMock: ID<number> = {
      id: 1,
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          notice: {
            id: 1
          }
        }
      }
    });

    await flushPromises();

    const response = await NoticeApi.remove(noticeSubmitMock);

    expect(response).toStrictEqual({
      errors: false,
    });
  });

  test('should remove with successfully', async () => {
    const noticeSubmitMock: ID<number> = {
      id: 1,
    };

    const errorsMock: ErrorResponse = {
      message: 'string',
      extensions: {
        message: 'string',
      }
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        errors: errorsMock,
        data: {
          notice: []
        }
      }
    });

    await flushPromises();

    const response = await NoticeApi.remove(noticeSubmitMock);

    expect(response).toStrictEqual({
      errors: true,
    });
  });

});