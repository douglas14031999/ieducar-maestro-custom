import { AccessLevel } from '@/types';
import authenticate from '@/middlewares/authenticate';
import { routes } from '@/router/routes';

export default routes([
  {
    path: 'processos',
    name: 'processes',
    meta: {
      name: 'Processos',
      level: AccessLevel.admin,
    },
    component: () =>
      import(
        /* webpackChunkName: "processes" */ './components/ProcessList.vue'
      ),
    beforeEnter: authenticate,
  },
  {
    name: 'process.create',
    path: '/processos/novo',
    meta: {
      name: 'Novo Processo',
      level: AccessLevel.admin,
    },
    component: () =>
      import(
        /* webpackChunkName: "processes" */ './components/ProcessCreate.vue'
      ),
    beforeEnter: authenticate,
    props: true,
  },
  {
    name: 'process.update',
    path: '/processos/:id/editar',
    meta: {
      name: 'Editar Processo',
      level: AccessLevel.admin,
    },
    component: () =>
      import(
        /* webpackChunkName: "processes" */ './components/ProcessCreate.vue'
      ),
    beforeEnter: authenticate,
    props: true,
  },
  {
    path: '/processos/:id/campos',
    name: 'process.fields',
    meta: {
      name: 'Campos do Processo',
      level: AccessLevel.admin,
    },
    component: () =>
      import(
        /* webpackChunkName: "processes" */ './components/ProcessFields.vue'
      ),
    beforeEnter: authenticate,
    props: true,
  },
  {
    name: 'process.periods',
    path: '/processos/:id/periodos',
    meta: {
      name: 'Períodos do Processo',
      level: AccessLevel.admin,
    },
    component: () =>
      import(
        /* webpackChunkName: "processes" */ './components/ProcessStages.vue'
      ),
    beforeEnter: authenticate,
    props: true,
  },
  {
    path: 'processos/:id/validar',
    name: 'process.check',
    meta: {
      name: 'Validar Processo',
      level: AccessLevel.admin,
    },
    component: () =>
      import(
        /* webpackChunkName: "processes" */ './components/ProcessCheck.vue'
      ),
    beforeEnter: authenticate,
    props: true,
  },
  {
    path: 'processos/:id',
    name: 'process.show',
    meta: {
      name: 'Processo',
      level: AccessLevel.admin,
    },
    component: () =>
      import(
        /* webpackChunkName: "processes" */ './components/ProcessShow.vue'
      ),
    beforeEnter: authenticate,
  },
]);
