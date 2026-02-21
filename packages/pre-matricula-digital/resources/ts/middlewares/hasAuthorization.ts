import { NavigationGuardNext, RouteLocationNormalized } from 'vue-router';
import { useGeneralStore } from '@/store/general';

export default async (
  to: RouteLocationNormalized,
  next: NavigationGuardNext
) => {
  const store = useGeneralStore();

  const hasAuthorization =
    store.isAuthenticated && (store.isAdmin || store.isCoordinator);
  if (!hasAuthorization && to.meta.level) {
    return next('/acesso-negado');
  }

  return next();
};
