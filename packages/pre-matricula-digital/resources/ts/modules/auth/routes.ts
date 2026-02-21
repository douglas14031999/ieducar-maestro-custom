import { routes } from '@/router/routes';

export default routes([
  {
    name: 'login',
    path: '/login',
    meta: {
      public: true,
      name: 'Login Administrativo',
    },
    component: () =>
      import(/* webpackChunkName: "auth" */ './components/Login.vue'),
  },
]);
