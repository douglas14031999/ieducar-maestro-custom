<template>
  <ul class="align-items-sm-end mr-auto navbar-nav">
    <li
      v-for="item in items"
      :key="item.id + '-public-menu-item'"
      class="nav-item mt-2 mt-lg-0 ml-0 ml-lg-3"
    >
      <a v-if="item.external" :href="item.to" :class="item.class">
        {{ item.name }}
      </a>
      <router-link
        v-else
        :class="[item.class, { active: $route.path === item.to }]"
        :to="item.to"
      >
        {{ item.name }}
      </router-link>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useGeneralStore } from '@/store/general';

const store = useGeneralStore();

const items = ref(
  [
    {
      id: 1,
      name: 'Página Inicial',
      class: 'nav-link text-primary',
      to: '/',
    },
    {
      id: 2,
      name: 'Avisos',
      class: 'nav-link text-primary',
      to: '/avisos',
    },
    {
      id: 3,
      name: 'Consultar Escola',
      class: 'nav-link text-primary',
      to: '/consultar-escola',
    },
    {
      id: 5,
      name: 'Login Pais e Alunos',
      class: 'btn btn-primary mr-2 login-btn',
      to: store.config.link_to_restrict_area,
      external: true,
    },
    {
      id: 4,
      name: 'Login Servidor',
      class: 'btn btn-primary mr-2 login-btn',
      to: '/login',
    },
  ].filter((link) => !!link.to)
);
</script>
