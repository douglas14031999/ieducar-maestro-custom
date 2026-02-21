<template>
  <main class="container" style="max-width: 740px">
    <h2 class="font-muli-20-primary">
      Escolha os campos solicitados no processo de pré-matrícula
    </h2>
    <p>
      Sabendo que os campos obrigatórios do sistema já estão inclusos, selecione
      os campos criados que deseja inserir no formulário desse processo de
      pré-matrícula.
    </p>
    <p>Você pode alterar a ordem dos campos, basta arrastá-los.</p>
    <skeleton-page-process-fields v-if="loadingData" />
    <p v-if="!loadingData" class="border-bottom mt-5 pb-3">
      Dados do(a) responsável
    </p>
    <configure-fields-responsible
      v-if="!loadingData"
      v-model="responsibleFields"
    />
    <p v-if="!loadingData" class="border-bottom mt-5 pb-3">
      Dados do(a) aluno(a)
    </p>
    <configure-fields-student v-if="!loadingData" v-model="studentFields" />
    <div v-if="!loadingData" class="row mt-5">
      <div class="offset-md-3 col-md-3">
        <x-btn
          data-test="button-back"
          label="Voltar"
          size="lg"
          color="gray-500"
          outline
          class="w-100"
          no-caps
          no-wrap
          @click="handleClickBack"
        />
      </div>
      <div class="col-md-3 mt-3 mt-md-0">
        <x-btn
          data-test="button-proceed"
          label="Prosseguir"
          :loading="loading"
          class="w-100 h-100 flex-row justify-content-center"
          color="primary"
          size="lg"
          no-caps
          no-wrap
          loading-normal
          @click="submit"
        />
      </div>
    </div>
  </main>
</template>

<script lang="ts">
import { Field, Fields, ProcessFieldList } from '@/modules/processes/types';
import { PropType, computed, defineComponent, onMounted, ref } from 'vue';
import ConfigureFields from './ConfigureFields.vue';
import { ProcessField as ProcessFieldApi } from '@/modules/processes/api';
import SkeletonPageProcessFields from '@/components/loaders/pages/PageProcessFields.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import { analytics } from '@/packages';
import { useLoader } from '@/composables';
import { useRouter } from 'vue-router';

export default defineComponent({
  components: {
    XBtn,
    ConfigureFieldsResponsible: ConfigureFields,
    ConfigureFieldsStudent: ConfigureFields,
    SkeletonPageProcessFields,
  },
  props: {
    id: {
      type: String as PropType<string>,
      required: true,
    },
    newProcess: {
      type: String as PropType<'true' | 'false'>,
      default: 'false',
    },
  },
  setup(props) {
    const { loader, loading: loadingData } = useLoader<ProcessFieldList>();

    const { loader: loaderSubmit, loading } = useLoader();

    const { page: pageview } = analytics();

    const router = useRouter();

    const responsibleFields = ref<Field[]>([]);
    const studentFields = ref<Field[]>([]);

    const fields = computed<Fields[]>(() => [
      ...responsibleFields.value
        .filter((f) => f.checked)
        .map((f, i) => ({
          field: f.id,
          order: i + 1,
          required: f.required,
          weight: f.priority ? Number(f.weight) : 0,
        })),
      ...studentFields.value
        .filter((f) => f.checked)
        .map((f, i) => ({
          field: f.id,
          order: i + 1,
          required: f.required,
          weight: f.priority ? Number(f.weight) : 0,
        })),
    ]);

    const getData = () => {
      loader(() =>
        ProcessFieldApi.list({
          id: props.id,
        })
      ).then((res) => {
        responsibleFields.value = res.responsibleFields;
        studentFields.value = res.studentFields;

        if (props.newProcess === 'true') {
          pageview({
            path: '/processos/criar-novo/dados',
          });
        } else {
          pageview({
            path: `/processos/${res.process.name
              .toLowerCase()
              .replaceAll(' ', '-')}/dados`,
          });
        }
      });
    };

    const submit = () => {
      loaderSubmit(() =>
        ProcessFieldApi.post({
          id: props.id,
          fields: fields.value,
        })
      ).then(() => {
        router.push({
          name: 'process.periods',
          params: {
            id: props.id,
            newProcess: props.newProcess,
          },
        });
      });
    };

    const handleClickBack = () => {
      router.push({
        name: 'process.update',
        params: {
          id: props.id,
          newProcess: props.newProcess,
        },
      });
    };

    onMounted(() => getData());

    return {
      loading,
      loadingData,
      responsibleFields,
      studentFields,
      fields,
      getData,
      submit,
      router,
      handleClickBack,
    };
  },
});
</script>
