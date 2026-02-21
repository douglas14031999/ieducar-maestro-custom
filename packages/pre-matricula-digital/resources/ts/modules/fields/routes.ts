import { AccessLevel } from '@/types';
import authenticate from '@/middlewares/authenticate';
import { routes } from '@/router/routes';

export default routes([
  {
    path: 'campos',
    name: 'fields',
    meta: {
      name: 'Campos',
      level: AccessLevel.admin,
    },
    component: () =>
      import(/* webpackChunkName: "fields" */ './components/FieldsPage.vue'),
    beforeEnter: authenticate,
  },
]);
