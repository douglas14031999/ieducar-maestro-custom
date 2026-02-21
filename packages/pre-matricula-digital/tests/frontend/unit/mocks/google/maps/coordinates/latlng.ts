import { vi } from 'vitest';

export class LatLng implements google.maps.LatLng {
  constructor(
    a: google.maps.LatLngLiteral | number,
    b?: boolean | number,
    c?: boolean
  ) {}

  public equals = vi
    .fn()
    .mockImplementation((other: google.maps.LatLng): boolean => false);
  public lat = vi.fn().mockImplementation((): number => 0);
  public lng = vi.fn().mockImplementation((): number => 0);
  public toString = vi.fn().mockImplementation((): string => '');
  public toUrlValue = vi
    .fn()
    .mockImplementation((precision?: number): string => '');
  public toJSON = vi.fn().mockImplementation((): google.maps.LatLngLiteral => {
    return { lat: 0, lng: 0 };
  });
}
