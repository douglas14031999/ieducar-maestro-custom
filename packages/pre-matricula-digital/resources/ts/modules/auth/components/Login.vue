<template>
  <main>
    <div class="text-center" v-html="message"></div>
  </main>
</template>

<script setup lang="ts">
import { Auth } from '@/modules/auth/types';
import { AuthRest } from '@/modules/auth/api';
import { ref } from 'vue';
import { useLoaderAndThrowError } from '@/composables';
import { useRouter } from 'vue-router';

const router = useRouter();

const message = ref<string>('Logando..');

const { loader } = useLoaderAndThrowError<Auth>();

loader(AuthRest.getAuth)
  .then((res) => {
    if (!res) {
      throw new Error();
    }

    router.push({
      name: 'preregistrations',
    });
  })
  .catch(() => {
    message.value = `
      O login de acesso no <strong>Pré-matrícula Digital</strong> é exclusivo para secretários e diretores escolares,
      ou gestores da Secretaria de Educação.
      <br>
      No momento, para acessar o sistema você deve estar logado no i-Educar.
    `;
  });
</script>
