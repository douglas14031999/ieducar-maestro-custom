import { routes } from '@/router/routes';

export default routes([
  {
    name: 'index',
    path: '',
    meta: {
      public: true,
      name: 'Página Inicial',
    },
    component: () =>
      import(/* webpackChunkName: "index" */ './components/Index.vue'),
  },
  {
    name: 'access-denied',
    path: '/acesso-negado',
    meta: {
      public: true,
      name: 'Acesso Negado',
    },
    component: () =>
      import(/* webpackChunkName: "index" */ './components/AccessDenied.vue'),
  },
]);
