import { flushPromises } from '@vue/test-utils';
import { City as CityApi, graphql } from '@/api/';
import { expect, vi } from 'vitest';

vi.mock('@/api');

describe('@/modules/processes/api/services/graphql/process', () => {
  test('should list successfully', async () => {
    const paramsMocked = {
      params: {
        search: 'string',
      }
    }

    const cityMocked = {
      key: 2,
      label: 'string',
      state: 'any',
    };

    vi.mocked(graphql).mockResolvedValueOnce({
      data: {
        data: {
          cities: {
            data: cityMocked
          }
        },
      },
    });

    await flushPromises();
  });
});
