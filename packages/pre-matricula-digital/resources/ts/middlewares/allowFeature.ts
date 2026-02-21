import { NavigationGuardNext, RouteLocationNormalized } from 'vue-router';
import { useGeneralStore } from '@/store/general';

export default async (
  to: RouteLocationNormalized,
  from: RouteLocationNormalized,
  next: NavigationGuardNext
) => {
  const store = useGeneralStore();

  const allow = store.features[to.meta.feature] || false;

  console.log(allow);

  if (allow === false) {
    return next('/acesso-negado');
  }

  return next();
};
