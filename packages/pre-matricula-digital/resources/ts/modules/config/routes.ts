import { AccessLevel } from '@/types';
import authenticate from '@/middlewares/authenticate';
import { routes } from '@/router/routes';

export default routes([
  {
    path: 'configuracoes',
    name: 'config',
    meta: {
      name: 'Configurações',
      level: AccessLevel.admin,
    },
    component: () =>
      import(
        /* webpackChunkName: "config" */ './components/Config.vue'
        ),
    beforeEnter: authenticate,
  },
]);
