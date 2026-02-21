<template>
  <div>
    <div class="row mt-3">
      <header-entity-data />
    </div>
    <div v-if="notice" class="row">
      <div class="col-12 col-lg-10 offset-lg-1">
        <h2 class="title-find-school">Avisos</h2>
        <div class="mt-4">
          <p
            data-test="notice-section"
            v-html="notice.text || 'Nenhum aviso cadastrado'"
          ></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import HeaderEntityData from '@/components/elements/HeaderEntityData.vue';
import { Notice } from '@/modules/notice/types';
import { Notice as NoticeApi } from '@/modules/notice/api';
import { useLoader } from '@/composables';

const { data: notice, loader } = useLoader<Notice>({
  id: null,
  text: null,
});

loader(NoticeApi.list).then((res) => {
  if (!res) {
    notice.value = {
      id: null,
      text: null,
    };
  }
});
</script>
