import { Nullable } from '@/types';

export interface Notice {
  id: Nullable<number>;
  text: Nullable<string>;
}

export interface NoticeSubmitResponse {
  errors: boolean;
  id?: number;
}
