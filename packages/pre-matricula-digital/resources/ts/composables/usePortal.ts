import { Nullable } from '@/types';
import { ref } from 'vue';

// eslint-disable-next-line func-names
export function usePortal() {
  const portalIsActive = ref(false);
  let portalEl: Nullable<HTMLElement> = null;

  function showPortal(isReady?: boolean) {
    if (isReady === true) {
      return;
    }

    portalIsActive.value = true;
  }

  function hidePortal() {
    portalIsActive.value = false;

    if (portalEl !== null) {
      portalEl = null;
    }
  }

  return {
    hidePortal,
    showPortal,
    portalIsActive,
  };
}
