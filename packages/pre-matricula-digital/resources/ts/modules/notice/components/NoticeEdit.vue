<template>
  <div>
    <skeleton-page-edit-notices v-if="loadingData" />
    <div v-if="!loadingData && notice">
      <div class="row mt-5">
        <div class="col-12 col-lg-10 offset-lg-1">
          <h2 class="title-find-school">Avisos</h2>
          <div class="mt-4">
            <p class="font-hind text-justify">
              Ao inserir conteúdo no editor de texto abaixo a mensagem será
              apresentada na tela inicial de inscrições para os responsáveis na
              seção Avisos, respeitando a formatação definida. Use o espaço para
              inserir orientações aos responsáveis no processo de pré-matrícula
              do município.
            </p>
          </div>
          <div class="mt-4">
            <x-field
              v-model="notice.text"
              container-class="col-12 p-0"
              name="text"
              type="RICH_TEXT"
            />
          </div>
        </div>
      </div>
      <div class="mt-2 col-12 col-lg-10 offset-lg-1 p-0">
        <div class="d-flex justify-content-end">
          <x-btn
            :loading="loading"
            label="Salvar"
            color="primary"
            class="w-100 col-12 col-md-2"
            no-caps
            no-wrap
            loading-normal
            @click="submit"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { AppContext, getCurrentInstance, ref } from 'vue';
import { Notice, NoticeSubmitResponse } from '@/modules/notice/types';
import {
  useLoader,
  useLoaderAndShowErrorByModal,
  useModal,
} from '@/composables';
import { Notice as NoticeApi } from '@/modules/notice/api';
import SkeletonPageEditNotices from '@/components/loaders/pages/PageEditNotices.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XField from '@/components/x-form/XField.vue';

const { dialog } = useModal();

const {
  data: notice,
  loading: loadingData,
  loader,
} = useLoader<Notice>({
  id: null,
  text: null,
});

const appContext = getCurrentInstance()?.appContext;

const { loader: loaderSubmit, loading } =
  useLoaderAndShowErrorByModal<NoticeSubmitResponse>(
    appContext as AppContext,
    ref({
      title: 'Erro',
      description:
        'Não foi possível salvar o aviso. Por favor, entre em contato com o suporte.',
    })
  );

const submit = () => {
  const { id, text } = notice.value;
  if (!!id === false && !!text === false) return;
  if (!!notice.value.text === false) {
    deleteNotice();
  } else {
    saveNotice();
  }
};

const deleteNotice = () => {
  const { id } = notice.value;

  loaderSubmit(() =>
    NoticeApi.remove({
      id: id as number,
    })
  )
    .then(() => {
      dialog({
        title: 'Sucesso!',
        titleClass: 'success',
        description: 'Aviso removido com sucesso!',
      });

      notice.value.id = null;
    })
    .finally(() => {
      loading.value = false;
    });
};

const saveNotice = () => {
  loaderSubmit(() => NoticeApi.post(notice.value))
    .then(({ id }) => {
      dialog({
        title: 'Sucesso!',
        titleClass: 'success',
        description: 'Aviso salvo com sucesso!',
      });

      notice.value.id = id as number;
    })
    .finally(() => {
      loading.value = false;
    });
};

loader(NoticeApi.list).then((res) => {
  if (!res) {
    notice.value = {
      id: null,
      text: null,
    };
  }
});
</script>
