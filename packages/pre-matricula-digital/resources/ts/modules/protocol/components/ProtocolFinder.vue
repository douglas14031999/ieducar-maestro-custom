<template>
  <div>
    <div class="row">
      <protocol-pre-find />
    </div>
    <div class="d-flex justify-content-md-center mt-5 row">
      <div class="col-md-3">
        <x-btn
          data-test="btn-back"
          color="gray-500"
          outline
          class="w-100"
          label="Voltar"
          no-caps
          no-wrap
          @click="prev"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ProtocolPreFind from '@/modules/protocol/components/ProtocolPreFind.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const prev = () => {
  router.push('/');
};
</script>
