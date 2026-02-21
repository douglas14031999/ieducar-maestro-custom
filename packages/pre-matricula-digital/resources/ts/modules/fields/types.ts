import { Nullable } from '@/types';

export type SetValues = (fields: Record<string, unknown>) => void;

export interface FieldPageOption {
  id?: string;
  name: Nullable<string>;
  weight: number;
}

export interface FieldPage {
  id: Nullable<string>;
  name: Nullable<string>;
  description: Nullable<string>;
  group: Nullable<'RESPONSIBLE' | 'STUDENT'>;
  type: Nullable<string>;
  internal: boolean;
  options: FieldPageOption[];
  deleteOptions?: string[];
  required?: boolean;
}

export interface ErrorResponse {
  message: string;
}
