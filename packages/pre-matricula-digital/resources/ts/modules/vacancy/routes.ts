import authenticate from '@/middlewares/authenticate';
import { routes } from '@/router/routes';

export default routes([
  {
    name: 'vacancies',
    path: '/vagas',
    meta: {
      name: 'Painel de Vagas',
    },
    component: () =>
      import(/* webpackChunkName: "vacancy" */ './components/Vacancies.vue'),
    beforeEnter: authenticate,
  },
]);
