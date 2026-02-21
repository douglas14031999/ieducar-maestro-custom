import { AccessLevel } from '@/types';
import authenticate from '@/middlewares/authenticate';
import { routes } from '@/router/routes';

export default routes([
  {
    name: 'notices',
    path: '/avisos',
    meta: {
      public: true,
      name: 'Avisos',
    },
    component: () =>
      import(/* webpackChunkName: "notices" */ './components/NoticeShow.vue'),
  },
  {
    name: 'notices.edit',
    path: '/avisos/editar',
    meta: {
      name: 'Avisos',
      level: AccessLevel.admin,
    },
    component: () =>
      import(/* webpackChunkName: "notices" */ './components/NoticeEdit.vue'),
    beforeEnter: authenticate,
  },
]);
