import axios, { AxiosInstance } from 'axios';

const token = window.config ? window.config.token : '';

export const http: AxiosInstance = axios.create({
  baseURL: String(import.meta.env.VITE_APP_API_URL || ''),
  headers: {
    Authorization: `Token ${token}`,
  },
});

axios.interceptors.response.use(
  (res) => {
    /**
     * If the response from the server has the "errors" object or the object "data" is empty,
     * here it is forced to go to the catch block automatically.
     */
    if (
      (res.data.errors || Object.keys(res.data).length === 0) &&
      !(res.data instanceof Blob)
    ) {
      return Promise.reject({
        response: {
          data: res.data,
        },
      });
    }

    return res;
  },
  (err) => Promise.reject(err)
);

export const rest = <T>(
  type: 'get' | 'post' | 'put' | 'delete',
  url: string,
  params?: object
): Promise<T> => http[type](url, params);

export const graphql = <T>(data: object, config?: object): Promise<T> =>
  axios.post('/graphql', data, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
    ...config,
  });
