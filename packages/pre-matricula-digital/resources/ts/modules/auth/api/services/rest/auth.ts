import { Auth } from '@/modules/auth/types';
import { http } from '@/modules/auth/api';

export const getAuth = (): Promise<Auth> =>
  http
    .get('/auth/login', {
      withCredentials: true,
    })
    .then((res) => res.data);
