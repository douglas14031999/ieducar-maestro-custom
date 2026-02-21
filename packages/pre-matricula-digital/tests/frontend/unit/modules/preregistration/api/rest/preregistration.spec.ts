import { flushPromises } from '@vue/test-utils';
import { PreregistrationRest, rest } from '@/modules/preregistration/api';
import { expect, vi } from 'vitest';

vi.mock('@/api');

describe('@/modules/preregistration/api/services/rest/preregistration', () => {
  test('should toExport successfully', async () => {
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

    const blobMock = new Blob(['Test']);

    vi.mocked(rest).mockResolvedValueOnce({
      data: blobMock,
    });

    await flushPromises();

    const response = await PreregistrationRest.toExport(filterMock);

    expect(response).toStrictEqual(blobMock);
  });

  test('should toReport successfully', async () => {
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

    const blobMock = new Blob(['Test']);

    vi.mocked(rest).mockResolvedValueOnce({
      data: blobMock,
    });

    await flushPromises();

    const response = await PreregistrationRest.toReport(filterMock);

    expect(response).toStrictEqual(blobMock);
  });
});
