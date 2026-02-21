export function formatDate(date: string): string {
  if (!date) return '';
  const [year, month, day] = date.split('-');
  return `${day}/${month}/${year}`;
}

export function formatCpf(cpf: string): string {
  if (!cpf) return '';
  cpf = cpf.replace(/\D/g, '').padStart(11, '0');
  return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

export function formatPhone(phone: string): string {
  if (!phone) return '';
  phone = phone.replace(/\D/g, '').padStart(11, '0');
  return phone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
}
