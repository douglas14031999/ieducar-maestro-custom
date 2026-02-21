<template>
  <main class="container" style="max-width: 740px">
    <h1>Configurações</h1>
    <p>
      Aqui você poderá parametrizar o sistema para ativar ou desativar certas funcionalidades assim como configurá-las.
    </p>
    <hr/>
    <h2 class="font-muli-20-primary">Edição de dados</h2>
    <p>
      Define se será possível modificar uma inscrição e/ou atualizar dados dos alunos.
    </p>
    <x-field
      v-model="config.allowPreregistrationDataUpdate"
      name="allowPreregistrationDataUpdate"
      container-class="form-group"
      type="CHECKBOX"
      label="Permitir a edição das inscrições"
      description="Quando marcado irá permitir que usuários editem a inscrição"
    />
    <x-field
      v-model="config.allowExternalSystemDataUpdate"
      name="allowExternalSystemDataUpdate"
      container-class="form-group"
      type="CHECKBOX"
      label="Permitir a atualização dos dados no momento do deferimento"
      description="Quando marcado irá permitir que usuários atualizem os dados no cadastro geral do aluno"
    />
    <button
      type="button"
      class="btn btn-primary"
      @click="saveConfig"
    >
      <span v-if="loadingConfig">
        Salvando..
      </span>
      <span v-else>
        Salvar atualizações
      </span>
    </button>
    <hr/>
    <h2 class="font-muli-20-primary">Transferência</h2>
    <p>
      Define se uma aluno será transferido caso já exista uma matrícula ativa para o ano letivo.
    </p>
    <x-field
      v-model="config.allowTransferRegistration"
      name="allowTransferRegistration"
      container-class="form-group"
      type="CHECKBOX"
      label="Fazer transferência ao deferir"
      description="Quando marcado irá transferir o aluno de escola se já houver uma matrícula ativa"
    />
    <x-field
      v-if="config.allowTransferRegistration"
      v-model="config.transferDescription"
      name="transferDescription"
      container-class="form-group"
      type="TEXT"
      label="Tipo de transferência"
      description="Descrição do tipo de transferência que será registrada"
      :errors="!config.transferDescription"
    />
    <button
      type="button"
      class="btn btn-primary"
      @click="saveConfig"
    >
      <span v-if="loadingConfig">
        Salvando..
      </span>
      <span v-else>
        Salvar atualizações
      </span>
    </button>
    <hr/>
    <h2 class="font-muli-20-primary">Atestado de Vaga</h2>
    <p>
      Define se será possível emitir atestado de vaga para inscrições com status "Deferido" e "Em convocação".
    </p>
    <x-field
      v-model="config.allowVacancyCertificate"
      name="allowVacancyCertificate"
      container-class="form-group"
      type="CHECKBOX"
      label="Permitir emissão de atestado de vaga"
      description="Quando marcado irá permitir a emissão de atestado de vaga para inscrições deferidas ou em convocação"
    />
    <button
      type="button"
      class="btn btn-primary"
      @click="saveConfig"
    >
      <span v-if="loadingConfig">
        Salvando..
      </span>
      <span v-else>
        Salvar atualizações
      </span>
    </button>
    <hr/>
    <h2 class="font-muli-20-primary">Agrupadores de processos</h2>
    <p>
      O agrupador de processos permite que restrições específicas sejam aplicadas a diversos processos possibilitando o
      compartilhamento de parametrizações entre eles.
    </p>
    <table class="table table-responsive table-striped table-vcenter">
      <thead>
      <tr>
        <th>Nome</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-if="groupers.length === 0">
        <td colspan="2">Nenhum agrupador de processos encontrado.</td>
      </tr>
      <tr v-for="(item, i) in groupers" :key="i">
        <td style="vertical-align: middle">
          <div>{{ item.name }}</div>
          <div class="small text-muted">Limite de inscrições em lista de espera: {{ item.waitingListLimit }}</div>
          <div v-if="item.processes.length" class="small text-muted">Processos deste grupo:</div>
          <div v-else class="small text-muted">Não há processos neste grupo</div>
          <div class="small text-muted">
            <ul v-if="item.processes.length">
              <li v-for="(p, k) in item.processes" :key="k">{{ p.name }}</li>
            </ul>
          </div>
        </td>
        <td class="text-right">
          <x-btn
            data-test="btn-reject-in-batch"
            class="mr-2 border-rejected text-rejected"
            label="Excluir"
            no-caps
            no-wrap
            size="sm"
            @click="destroy({ id: item.id })"
          />
        </td>
      </tr>
      </tbody>
    </table>
    <h3>Adicionar novo agrupador</h3>
    <x-form v-slot="slot" @submit="submit">
      <x-field
        v-model="grouper.name"
        name="name"
        label="Nome do Agrupador de Processos"
        type="TEXT"
        rules="required"
        :errors="!!slot.errors.name"
        container-class="col-12"
      />
      <x-field
        v-model="grouper.waitingListLimit"
        name="waitingListLimit"
        label="Limite de inscrições em lista de espera"
        description="Será o limite de inscrições compartilhada entre todos os processos"
        type="NUMBER"
        rules="required"
        :errors="!!slot.errors.waitingListLimit"
        container-class="col-12"
      />
      <x-field
        v-model="grouper.processes"
        name="processes"
        label="Processos"
        description="Selecione os processos que serão agrupados"
        type="MULTISELECT"
        :options="processes.map((e: any) => ({ label: e.name, value: e.id }))"
        :errors="!!slot.errors.processes"
        container-class="col-12"
      />
      <div class="col-6">
        <button
          type="submit"
          class="btn btn-block btn-primary"
        >
          Adicionar
        </button>
      </div>
    </x-form>
  </main>
</template>

<script lang="ts">
import { ID, Processes } from "@/types";
import { Process as ProcessApi, ProcessGrouper as ProcessGrouperApi } from '@/modules/processes/api';
import { defineComponent, ref} from 'vue';
import { GenericXForm } from '@/components/x-form/XForm.vue';
import { ProcessGrouper } from "@/modules/processes/types";
import XBtn from "@/components/elements/buttons/XBtn.vue";
import XField from "@/components/x-form/XField.vue";
import { graphql } from '@/api/api';
import { useGeneralStore } from "@/store/general";
import { useLoader } from "@/composables";

type Grouper = {
  name: string;
  waitingListLimit: number;
  processes: ID[];
}

export default defineComponent({
  components: {
    XBtn,
    XField,
    XForm: GenericXForm<Grouper>(),
  },
  setup() {
    const store = useGeneralStore();

    const config = ref({
      allowPreregistrationDataUpdate: store.features.allowPreregistrationDataUpdate,
      allowExternalSystemDataUpdate: store.features.allowExternalSystemDataUpdate,
      allowTransferRegistration: store.features.allowTransferRegistration,
      transferDescription: store.features.transferDescription,
      allowVacancyCertificate: store.features.allowVacancyCertificate,
    });

    const loadingConfig = ref(false);

    const grouper = ref({
      name: undefined,
      waitingListLimit: 1,
      processes: [],
    });

    const { loader, data: groupers } = useLoader<ProcessGrouper[]>([]);
    const { loader: loaderSave } = useLoader<ProcessGrouper>();
    const { loader: loaderDelete } = useLoader<{
      deleteProcessGrouper: ProcessGrouper,
    }>();
    const { loader: loaderProcesses, data: processes } = useLoader<Processes[]>([]);

    loader(ProcessGrouperApi.list);
    loaderProcesses(() => ProcessApi.list());

    const submit = (model: Grouper, { resetForm }) => {
      loaderSave(() => ProcessGrouperApi.post({
        name: model.name,
        waitingListLimit: Number(model.waitingListLimit),
        processes: model.processes,
      })).finally(() => loader(ProcessGrouperApi.list));

      grouper.value.name = undefined;
      grouper.value.waitingListLimit = 1;

      resetForm();
    };

    const destroy = (id: ID) => {
      loaderDelete(() => ProcessGrouperApi.remove(id)).finally(() => loader(ProcessGrouperApi.list));
    };

    const saveConfig = () => {
      loadingConfig.value = true;

      graphql({
        query: `
          mutation saveConfig($input: [ConfigInput!]!) {
            saveConfig(
              input: $input
            )
          }
        `,
        variables: {
          input: [
            {
              key: 'allow_preregistration_data_update',
              value: config.value.allowPreregistrationDataUpdate ? 'true' : 'false',
            },
            {
              key: 'allow_external_system_data_update',
              value: config.value.allowExternalSystemDataUpdate ? 'true' : 'false',
            },
            {
              key: 'allow_transfer_registration',
              value: config.value.allowTransferRegistration ? 'true' : 'false',
            },
            {
              key: 'transfer_description',
              value: config.value.transferDescription,
            },
            {
              key: 'allow_vacancy_certificate',
              value: config.value.allowVacancyCertificate ? 'true' : 'false',
            },
          ],
        },
      }).then(() => {
        store.features.allowPreregistrationDataUpdate = config.value.allowPreregistrationDataUpdate;
        store.features.allowExternalSystemDataUpdate = config.value.allowExternalSystemDataUpdate;
        store.features.allowTransferRegistration = config.value.allowTransferRegistration;
        store.features.transferDescription = config.value.transferDescription;
        store.features.allowVacancyCertificate = config.value.allowVacancyCertificate;
      }).catch((res: unknown) => console.log(res)).finally(() => {
        loadingConfig.value = false;
      });
    }

    return {
      config,
      processes,
      groupers,
      grouper,
      loadingConfig,
      destroy,
      submit,
      saveConfig,
    };
  },
});
</script>
