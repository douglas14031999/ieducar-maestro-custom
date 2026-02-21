import { routes } from '@/router/routes';

export default routes([
  {
    name: 'protocol.status',
    path: '/protocolo/:id',
    meta: {
      public: true,
      name: 'Resultado de Consulta',
    },
    component: () =>
      import(
        /* webpackChunkName: "protocol" */ './components/ProtocolStatus.vue'
      ),
  },
  {
    name: 'protocol.finder',
    path: '/onde-encontro-o-protocolo',
    meta: {
      public: true,
      name: 'Onde encontro o Protocolo',
    },
    component: () =>
      import(
        /* webpackChunkName: "protocol" */ './components/ProtocolFinder.vue'
      ),
  },
  {
    name: 'protocol.find.by.cpf',
    path: '/busca-protocolo-cpf',
    meta: {
      public: true,
      name: 'Busca por CPF',
    },
    component: () =>
      import(
        /* webpackChunkName: "protocol" */ './components/ProtocolFindByCpf.vue'
      ),
  },
  {
    name: 'protocol.find.by.rg',
    path: '/busca-protocolo-rg',
    meta: {
      public: true,
      name: 'Busca por RG',
    },
    component: () =>
      import(
        /* webpackChunkName: "protocol" */ './components/ProtocolFindByRg.vue'
      ),
  },
  {
    name: 'protocol.find.by.certidao',
    path: '/busca-protocolo-certidao',
    meta: {
      public: true,
      name: 'Busca por Certidão de Nascimento',
    },
    component: () =>
      import(
        /* webpackChunkName: "protocol" */ './components/ProtocolFindByCertidao.vue'
      ),
  },
  {
    name: 'protocol.find.by.nome.data.nascimento',
    path: '/busca-protocolo-nome-e-nascimento',
    meta: {
      public: true,
      name: 'Busca por Nome e Data de Nascimento',
    },
    component: () =>
      import(
        /* webpackChunkName: "protocol" */ './components/ProtocolFindByNomeDataNascimento.vue'
      ),
  },
]);
