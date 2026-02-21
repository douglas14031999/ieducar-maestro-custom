<template>
  <ul class="align-items-sm-end mr-auto navbar-nav">
    <li class="nav-item mt-2 mt-lg-0 ml-0 ml-lg-3">
      <router-link
        class="nav-link text-primary"
        to="/inscricoes"
        active-class="active"
      >
        Inscrições
      </router-link>
    </li>
    <li class="nav-item mt-2 mt-lg-0 ml-0 ml-lg-3">
      <router-link
        class="nav-link text-primary"
        active-class="active"
        to="/vagas"
      >
        Painel de Vagas
      </router-link>
    </li>
    <li v-if="hasAuthorization" class="nav-item mt-2 mt-lg-0 ml-0 ml-lg-3">
      <router-link
        class="nav-link text-primary"
        active-class="active"
        to="/processos"
      >
        Processos
      </router-link>
    </li>
    <li v-if="hasAuthorization" class="nav-item mt-2 mt-lg-0 ml-0 ml-lg-3">
      <router-link
        class="nav-link text-primary"
        active-class="active"
        to="/configuracoes"
      >
        Configurações
      </router-link>
    </li>
    <li v-if="hasAuthorization" class="nav-item mt-2 mt-lg-0 ml-0 ml-lg-3">
      <router-link
        :to="{ name: 'fields' }"
        class="nav-link text-primary"
        active-class="active"
      >
        Campos
      </router-link>
    </li>
    <li v-if="hasAuthorization" class="nav-item mt-3 mt-md-0">
      <router-link
        to="/avisos/editar"
        class="nav-link text-primary"
        exact
        active-class="active"
      >
        Avisos
      </router-link>
    </li>
    <li class="nav-item mt-3 mt-md-0">
      <router-link
        to="/consultar-escola"
        class="nav-link text-primary"
        exact
        active-class="active"
      >
        Consultar Escola
      </router-link>
    </li>
    <li class="nav-item mt-2 mt-lg-0 ml-0 ml-lg-3">
      <a class="nav-link disabled" href="#">{{ getAuthenticatedUser?.name }}</a>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useGeneralStore } from '@/store/general';

const store = useGeneralStore();

const getAuthenticatedUser = computed(() => store.getAuthenticatedUser);

const hasAuthorization = computed(
  () => store.isAuthenticated && (store.isAdmin || store.isCoordinator)
);
</script>
