import { vi } from 'vitest';

export const MapsEventListener: google.maps.MapsEventListener = {
  remove: vi.fn(),
};
