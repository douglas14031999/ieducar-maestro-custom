import Honeybadger, { useHoneybadger } from 'honeybadger-vue3-composition-api';
import { useGeneralStore } from '@/store/general';

export const honeyBadgerConfig = {
  apiKey: import.meta.env.VITE_HONEYBADGER_KEY,
  environment: import.meta.env.VITE_AMBIENT_MODE,
};

export const callHoneybadgerError = (error: Error) => {
  const honeybadger = useHoneybadger();

  const store = useGeneralStore();

  honeybadger.setContext({
    config: window.config,
    user: store.auth.user,
  });

  honeybadger.notify(error as unknown as string);
};

export { Honeybadger };
