<template>
  <main>
    <div class="d-flex flex-column justify-content-center align-items-center">
      <h2 class="text-danger text-center">
        Você não tem permissão para acessar essa página.
      </h2>
      <x-btn
        class="bg-white text-primary border-primary"
        size="md"
        @click="$router.push('/')"
      >
        Ir a página inicial
      </x-btn>
    </div>
  </main>
</template>

<script setup lang="ts">
import XBtn from '@/components/elements/buttons/XBtn.vue';
</script>
