/* eslint-disable */
export {};

declare global {
  interface Array<T> {
    sortBy(key: string | number): Array<T>;
  }
}

if (!Array.prototype.sortBy) {
  Array.prototype.sortBy = function <T>(key: string | number): T[] {
    return this.sort((a: any, b: any) => (a[key] > b[key] ? 1 : -1));
  };
}
