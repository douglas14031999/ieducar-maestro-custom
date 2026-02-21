export default function rg(rg: string): boolean {
  return (
    /^0+$/.test(rg) ||
    /^1+$/.test(rg) ||
    /^2+$/.test(rg) ||
    /^3+$/.test(rg) ||
    /^4+$/.test(rg) ||
    /^5+$/.test(rg) ||
    /^6+$/.test(rg) ||
    /^7+$/.test(rg) ||
    /^8+$/.test(rg) ||
    /^9+$/.test(rg)
  );
}
