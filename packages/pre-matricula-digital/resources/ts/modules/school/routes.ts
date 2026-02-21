import { routes } from '@/router/routes';

export default routes([
  {
    name: 'school.find',
    path: '/consultar-escola',
    meta: {
      public: true,
      name: 'Consultar Escola',
    },
    component: () =>
      import(/* webpackChunkName: "public" */ './components/SchoolFind.vue'),
  },
]);
