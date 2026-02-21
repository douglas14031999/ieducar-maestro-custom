import { AppContext, Ref } from 'vue';
import { ErrorResponse } from '@/types';
import { useLoader } from './useLoader';
import { useModal } from '@/composables/useModal';

interface ModalErrorContent {
  title: string;
  titleClass?: string;
  iconLeft?: string;
  description: string;
}

export function useLoaderAndThrowError<T>(initialData?: T) {
  return useLoader<T>(
    initialData,
    (error: Error, reject: typeof Promise.reject) => reject(error)
  );
}

export function useLoaderAndShowErrorByToast<T>(initialData?: T) {
  return useLoader<T>(initialData, (error: Error) => {
    console.log(error);

    // Example
    // toast.error(error.message);
  });
}

export function useLoaderAndShowErrorByModal<T>(
  appContext: AppContext,
  payload?: Ref<ModalErrorContent>,
  initialData?: T
) {
  return useLoader<T>(initialData, (error: Error) => {
    const { dialog } = useModal(appContext);
    const err = error as unknown as {
      response: {
        data: {
          errors: ErrorResponse[];
        };
      };
    };

    dialog({
      title: payload?.value.title || err.response.data.errors[0].message,
      titleClass: payload?.value.titleClass || 'danger',
      iconLeft: payload?.value.iconLeft || 'status-red',
      description:
        payload?.value.description ||
        err.response.data.errors[0].extensions?.message,
    });
  });
}
