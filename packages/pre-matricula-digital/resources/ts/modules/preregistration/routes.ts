import allowFeature from '@/middlewares/allowFeature';
import authenticate from '@/middlewares/authenticate';
import { routes } from '@/router/routes';

export default routes([
  {
    name: 'preregistrations',
    path: '/inscricoes',
    meta: {
      load: true,
      name: 'Inscrições',
    },
    component: () =>
      import(
        /* webpackChunkName: "preregistrations" */ './components/PreRegistrationList.vue'
      ),
    children: [
      {
        name: 'preregistration.modal',
        path: ':protocol',
        meta: {
          name: 'Inscrição do Aluno',
        },
        component: () =>
          import(
            /* webpackChunkName: "preregistrations" */ './components/PreRegistrationModal.vue'
          ),
      },
      {
        name: 'preregistration.edit.modal',
        path: ':protocol/editar',
        meta: {
          name: 'Editar inscrição',
          feature: 'allowPreregistrationDataUpdate',
        },
        beforeEnter: allowFeature,
        component: () =>
          import(
            /* webpackChunkName: "preregistrations" */ './components/PreRegistrationEditModal.vue'
          ),
      },
    ],
    beforeEnter: authenticate,
  },
  {
    name: 'preregistration',
    path: '/inscricao/:id',
    component: () =>
      import(
        /* webpackChunkName: "prepregistrations-subscription" */ './components/PreRegistration.vue'
      ),
    meta: {
      public: true,
      name: 'Processo de Inscrição',
    },
    beforeEnter: authenticate,
  },
  {
    name: 'waiting-list',
    path: '/lista-de-espera/:id',
    component: () =>
      import(
        /* webpackChunkName: "prepregistrations-subscription" */ './components/PreRegistrationListPublic.vue'
      ),
    meta: {
      public: true,
      name: 'Consulta da lista de espera',
    },
    beforeEnter: authenticate,
  },
]);
