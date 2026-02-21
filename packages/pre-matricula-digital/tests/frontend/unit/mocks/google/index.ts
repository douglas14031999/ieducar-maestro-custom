import { LatLng } from './maps/coordinates/latlng';
import { Map_ } from './maps/maps/map';
import { Marker } from './drawing/marker/marker';
import { mockInstances } from './registry';
import { vi } from 'vitest';

export const initialize = function (): void {
  mockInstances.clearAll();
  (global as any).google = {
    maps: {
      Marker: Marker,
      Map: Map_,
      LatLng: LatLng,
      Geocoder: vi.fn(() => ({
        geocode: vi.fn(),
      })),
    },
  };
};
