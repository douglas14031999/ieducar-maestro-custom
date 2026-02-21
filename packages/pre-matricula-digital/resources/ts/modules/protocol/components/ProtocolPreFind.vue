<template>
  <div class="col-md-8 mt-5 offset-md-2">
    <h2 class="font-muli-20-primary mb-4">
      Identifique o(a) aluno(a) para encontrar o(s) protocolo(s):
    </h2>
    <p class="text-justify font-hind">
      Selecione uma das opções abaixo para validar se o(a)
      <strong>aluno(a)</strong> já possui alguma inscrição de pré-matrícula.
      Caso não seja possível identificá-lo com a opção selecionada, você pode
      escolher outro campo para validar a existência de protocolo(s).
    </p>
    <div class="mt-5">
      <button
        data-test="btn-1"
        type="button"
        class="btn btn-block btn-outline-primary mb-2"
        @click="navigate('/busca-protocolo-cpf')"
      >
        <span
          class="text-uppercase font-weight-lighter font-hind"
          style="font-size: 16px"
        >
          CPF do(a) aluno(a)
        </span>
      </button>
      <button
        data-test="btn-2"
        type="button"
        class="btn btn-block btn-outline-primary mb-2"
        @click="navigate('/busca-protocolo-rg')"
      >
        <span
          class="text-uppercase font-weight-lighter font-hind"
          style="font-size: 16px"
        >
          RG do(a) aluno(a)
        </span>
      </button>
      <button
        data-test="btn-3"
        type="button"
        class="btn btn-block btn-outline-primary mb-2"
        @click="navigate('/busca-protocolo-certidao')"
      >
        <span
          class="text-uppercase font-weight-lighter font-hind"
          style="font-size: 16px"
        >
          Certidão de nascimento do(a) aluno(a)
        </span>
      </button>
      <button
        data-test="btn-4"
        type="button"
        class="btn btn-block btn-outline-primary mb-2"
        @click="navigate('/busca-protocolo-nome-e-nascimento')"
      >
        <span
          class="text-uppercase font-weight-lighter font-hind"
          style="font-size: 16px"
        >
          Nome e data de nascimento do(a) aluno(a)
        </span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';

const router = useRouter();

const navigate = (path: string) => {
  router.push(path);
};
</script>
