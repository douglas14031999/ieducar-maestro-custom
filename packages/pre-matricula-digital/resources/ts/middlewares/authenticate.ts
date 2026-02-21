import { NavigationGuardNext, RouteLocationNormalized } from 'vue-router';
import hasAuthorization from '@/middlewares/hasAuthorization';
import { useGeneralStore } from '@/store/general';

// Se o usuário não foi autenticado e a rota for pública, autentica o usuário
// em segundo plano para evitar aguardar a requisição do login. Em casos de
// rotas privadas aguarda a requisição de login para determinar se o usuário
// tem acesso.

export default async (
  to: RouteLocationNormalized,
  from: RouteLocationNormalized,
  next: NavigationGuardNext
) => {
  const store = useGeneralStore();

  if (store.auth.checked === false && to.meta.public) {
    store.check().catch(() => store.checked());
  } else if (store.auth.checked === false) {
    await store.check().catch(() => store.checked());
  }

  if (to.meta.public) {
    return next();
  }

  if (store.auth.authenticated) {
    return hasAuthorization(to, next);
  }

  return next('/login');
};
