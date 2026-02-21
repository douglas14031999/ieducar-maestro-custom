import { expect, vi } from 'vitest';
import { isValidDate, isValidBornDate } from '@/datetime';

describe('datetime', () => {
  describe('isValidDate', () => {
    test('must be true if year 0000', () => {
      const date = '0000-06-20';

      const result = isValidDate(date);

      expect(result).toBe(true);
    });

    test('must be true if year is up to 1900', () => {
      const date = '1901-06-20';

      const result = isValidDate(date);

      expect(result).toBe(true);
    });

    test('must be false if year negative', () => {
      const date = '-1901-06-20';

      const result = isValidDate(date);

      expect(result).toBe(false);
    });

    test('must be false if date is not in YYYY-MM-DD format', () => {
      const dates = ['1901/06/20', '1901-06/20', '20/06/1901', '1991-20-6'];

      dates.forEach((date) => {
        const result = isValidDate(date);

        expect(result).toBe(false);
      });
    });
  });

  describe('isValidBornDate', () => {
    beforeEach(() => {
      vi.useFakeTimers();
    });

    afterEach(() => {
      vi.useRealTimers();
    });

    test('must be false if year is bellow 1900', () => {
      vi.setSystemTime('2021-06-20');

      const date = '1899-06-20';

      const result = isValidBornDate(date);

      expect(result).toBe(false);
    });

    test('must be false if year is above current year', () => {
      vi.setSystemTime('2021-06-20');

      const date = '2100-06-20';

      const result = isValidBornDate(date);

      expect(result).toBe(false);
    });

    test('must be true if year is on the year range between 1900 and current year', () => {
      vi.setSystemTime('2021-06-20 20:00:00');

      const dates = [
        '1901-06-20',
        '1980-06-20',
        '1990-06-20',
        '2000-06-20',
        '2010-06-20',
        '2020-06-20',
        '2021-06-19',
        '2021-06-20',
      ];

      dates.forEach((date) => {
        const result = isValidBornDate(date);

        expect(result).toBe(true);
      });
    });
  });
});
