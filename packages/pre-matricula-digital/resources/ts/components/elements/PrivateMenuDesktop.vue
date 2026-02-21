<template>
  <div
    class="pr-0 pr-lg-4 position-relative"
    style="height: 53px"
    @mouseleave="show = false"
  >
    <span class="pr-3 float-left authenticated_user" @mouseenter="show = true">
      {{ getAuthenticatedUser?.name }}
    </span>
    <x-btn
      class="d-none d-lg-block position-relative"
      style="border-width: 1px !important"
      :class="{
        'border-primary bg-primary text-white': show,
        'bg-white text-primary border-primary-light': !show,
      }"
      size="lg"
      @mouseenter="show = true"
    >
      <x-icon name="mdi-menu" size="28px" />
    </x-btn>
    <div class="pt-3">
      <div
        v-show="show"
        class="position-absolute bg-white border-radius-normal p-2 shadow-2"
        style="min-width: 300px; right: 1.5em; left: auto"
      >
        <router-link
          v-for="(item, index) in items"
          :key="index + '-menu-item'"
          :class="item.class"
          :to="item.to"
          active-class="active"
          class="mt-1"
          exact
          @click="show = false"
        >
          {{ item.name }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XIcon from '@/components/elements/icons/XIcon.vue';
import { useGeneralStore } from '@/store/general';

const show = ref(false);

const store = useGeneralStore();

const getAuthenticatedUser = computed(() => store.getAuthenticatedUser);

const hasAuthorization = computed(
  () => store.isAuthenticated && (store.isAdmin || store.isCoordinator)
);

const items = computed(() => {
  let items = [
    {
      id: 1,
      name: 'Inscrições',
      class: 'nav-link text-primary',
      to: '/inscricoes',
      public: false,
      hasAuthorization: true,
    },
    {
      id: 2,
      name: 'Painel de Vagas',
      class: 'nav-link text-primary',
      to: '/vagas',
      public: false,
      hasAuthorization: true,
    },
  ];
  if (hasAuthorization.value) {
    items = items.concat([
      {
        id: 3,
        name: 'Processos',
        class: 'nav-link text-primary',
        to: '/processos',
        public: false,
        hasAuthorization: true,
      },
      {
        id: 7,
        name: 'Configurações',
        class: 'nav-link text-primary',
        to: '/configuracoes',
        public: false,
        hasAuthorization: true,
      },
      {
        id: 4,
        name: 'Campos',
        class: 'nav-link text-primary',
        to: '/campos',
        public: false,
        hasAuthorization: true,
      },
      {
        id: 5,
        name: 'Avisos',
        class: 'nav-link text-primary',
        to: '/avisos/editar',
        public: false,
        hasAuthorization: true,
      },
    ]);
  }
  items = items.concat([
    {
      id: 6,
      name: 'Consultar Escola',
      class: 'nav-link text-primary',
      to: '/consultar-escola',
      public: true,
      hasAuthorization: false,
    },
  ]);

  return items;
});
</script>

<style lang="scss" scoped>
.authenticated_user {
  margin-top: 0.7em;
  font-family: Muli, sans-serif;
  font-weight: bold;
  font-size: 18px;
  color: rgba(0, 0, 0, 0.3);
}
</style>
