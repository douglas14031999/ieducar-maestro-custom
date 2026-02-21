<template>
  <main class="container" style="max-width: 740px">
    <h2 class="font-muli-20-primary">Crie o processo de pré-matrícula</h2>
    <p>
      Crie o processo de pré-matrícula que deseja liberar para o seu município.
      Escolha um nome, para qual ano as pré-matrículas serão válidas, selecione
      as séries que participarão desse processo e os turnos que deseja liberar.
    </p>
    <skeleton-page-process-create v-if="loadingContent" />
    <x-form
      v-if="!loadingContent"
      :loading="loading"
      :initial-values="process"
      :schema="schema"
      @submit="submit"
    >
      <template #default="{ values }">
        <div class="col-12">
          <div class="row">
            <div v-if="values.selectedSchools" class="col-12">
              <p class="border-bottom mt-5 pb-3">
                Selecione as escolas que deseja utilizar nesse processo de
                inscrição
              </p>
            </div>
            <div v-if="values.selectedSchools" class="col-12">
              <div class="row mt-3">
                <div
                  v-for="(school, i) in schools.sortBy('label')"
                  :key="i"
                  class="col-12 col-md-6 mb-3"
                >
                  <label class="toggle-checkbox">
                    <Field
                      as="input"
                      name="schools"
                      type="checkbox"
                      rules="required"
                      :value="school.key"
                    />
                    <div class="toggle-text">
                      {{ school.label }}
                    </div>
                  </label>
                </div>
                <div class="col-12">
                  <ErrorMessage
                    name="schools"
                    :class="'validator-rule-error'"
                  />
                </div>
              </div>
            </div>
            <div class="col-12">
              <p class="border-bottom mt-5 pb-3">
                Selecione as séries que deseja incluir nesse processo de
                inscrição
              </p>
            </div>
            <div class="col-12">
              <template v-for="course in courses">
                <div v-if="course.grades.length" :key="course.id">
                  <h4 class="m-0">
                    {{ course.name }}
                  </h4>
                  <div class="row">
                    <div
                      v-for="grade in course.grades"
                      :key="grade.id"
                      class="col-12 col-md-6 mb-3"
                    >
                      <label class="toggle-checkbox">
                        <Field
                          as="input"
                          name="grades"
                          type="checkbox"
                          rules="required"
                          :value="grade.id"
                        />
                        <div class="toggle-text">
                          {{ grade.name }}
                        </div>
                      </label>
                    </div>
                  </div>
                </div>
              </template>
              <ErrorMessage name="grades" :class="'validator-rule-error'" />
            </div>
            <div class="col-12">
              <p class="border-bottom mt-5 pb-3">
                Selecione os turnos que deseja utilizar nesse processo
              </p>
            </div>
            <div class="col-12">
              <div class="row">
                <div
                  v-for="period in periods"
                  :key="period.id"
                  class="col-12 col-md-6 mb-3"
                >
                  <label class="toggle-checkbox">
                    <Field
                      as="input"
                      name="periods"
                      type="checkbox"
                      rules="required"
                      :value="period.id"
                    />
                    <div class="toggle-text">
                      {{ period.name }}
                    </div>
                  </label>
                </div>
                <div class="col-12">
                  <ErrorMessage
                    name="periods"
                    :class="'validator-rule-error'"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </x-form>
  </main>
</template>

<script lang="ts">
import {
  Course,
  Period,
  ProcessCreate,
  ProcessCreateListCourses,
} from '@/modules/processes/types';
import { ErrorMessage, Field } from 'vee-validate';
import { FormSchema, ID, Option } from '@/types';
import { PropType, computed, defineComponent, ref } from 'vue';
import { GenericXForm } from '@/components/x-form/XForm.vue';
import { Process as ProcessApi } from '@/modules/processes/api';
import SkeletonPageProcessCreate from '@/components/loaders/pages/PageProcessCreate.vue';
import { analytics } from '@/packages';
import { useLoader } from '@/composables';
import { useRouter } from 'vue-router';

export default defineComponent({
  components: {
    ErrorMessage,
    Field,
    SkeletonPageProcessCreate,
    XForm: GenericXForm<ProcessCreate>(),
  },
  props: {
    id: {
      type: String as PropType<string>,
      required: false,
      default: undefined,
    },
    newProcess: {
      type: String as PropType<'true' | 'false'>,
      default: 'false',
    },
  },
  setup(props) {
    const { loader, loading: loadingProcess } =
      useLoader<ProcessCreateListCourses>();

    const {
      loader: loaderProcess,
      loading: loadingProcessData,
      data: process,
    } = useLoader<ProcessCreate>({
      id: null,
      name: null,
      active: true,
      schoolYear: null,
      grades: [],
      periods: [],
      selectedSchools: false,
      schools: [],
      messageFooter:
        'Acompanhe a publicação do resultado classificatório e convocação no site da Prefeitura.',
      gradeAgeRangeLink: null,
      forceSuggestedGrade: false,
      showPriorityProtocol: false,
      allowResponsibleSelectMapAddress: false,
      blockIncompatibleAgeGroup: false,
      autoRejectByDays: false,
      autoRejectDays: null,
      waitingListLimit: 1,
      minimumAge: null,
      rejectType: 'NO_REJECT',
      onePerYear: false,
      showWaitingList: false,
      criteria: null,
      grouper: null,
    });

    const { loader: loaderSubmit, loading } = useLoader<ID>();

    const { page: pageview } = analytics();

    const router = useRouter();

    const courses = ref<Course[]>([]);
    const periods = ref<Period[]>([]);
    const schoolYears = ref<Option[]>([]);
    const schools = ref<Option[]>([]);
    const groupers = ref<Option[]>([]);
    const rejectTypes = ref<Option[]>([
      { key: 'NO_REJECT', label: 'Não indeferir automaticamente' },
      {
        key: 'REJECT_SAME_PROCESS',
        label: 'Indeferir as outras inscrições do mesmo processo',
      },
      {
        key: 'REJECT_SAME_YEAR',
        label: 'Indeferir as outras inscrições do mesmo ano',
      },
    ]);

    const schema = computed<FormSchema>(
      () =>
        ({
          fields: [
            {
              type: 'TEXT',
              label: 'Nome do Processo',
              name: 'name',
              containerClass: 'col-12 col-md-8',
              value: process.value.name,
              rules: 'required',
            },
            {
              label: 'Ano Letivo',
              rules: 'required',
              type: 'SELECT',
              value: process.value.schoolYear,
              options: schoolYears.value,
              name: 'schoolYear',
              containerClass: 'col-12 col-md-4',
            },
            {
              label: 'Grupo do processo',
              type: 'SELECT',
              value: process.value.grouper,
              options: groupers.value,
              name: 'grouper',
              containerClass: 'col-12',
              clearable: true,
            },
            {
              type: 'RICH_TEXT',
              label: 'Mensagem de Rodapé',
              name: 'messageFooter',
              rules: '',
              containerClass: 'col-12',
              value: process.value.messageFooter,
            },
            {
              type: 'TEXT',
              label:
                'URL do arquivo com orientações de séries por faixa etária',
              name: 'gradeAgeRangeLink',
              rules: '',
              value: process.value.gradeAgeRangeLink,
              containerClass: 'col-12',
            },
            {
              type: 'NUMBER',
              label: 'Limite de inscrições em lista de espera',
              name: 'waitingListLimit',
              rules: '',
              value: process.value.waitingListLimit,
              containerClass: 'col-12',
            },
            {
              type: 'SELECT',
              label: 'Indeferimento automático',
              name: 'rejectType',
              rules: '',
              value: process.value.rejectType,
              options: rejectTypes.value,
              description:
                'Após deferir uma inscrição será feito o indeferimento automático de todas as outras inscrições do mesmo CPF.',
              containerClass: 'col-12',
            },
            {
              type: 'NUMBER',
              label: 'Idade mínima (em dias)',
              name: 'minimumAge',
              rules: '',
              value: process.value.minimumAge,
              containerClass: 'col-12',
            },
            {
              type: 'CHECKBOX',
              name: 'active',
              label: 'Apresentar período na tela inicial de inscrições',
              subLabel:
                'Quando marcado apresentará o período na tela inicial de inscrição independente da situação',
              value: process.value.active,
              rules: '',
              containerClass: 'col-12',
            },
            {
              type: 'CHECKBOX',
              name: 'showPriorityProtocol',
              label: 'Apresentar posição do(a) aluno(a) no protocolo',
              subLabel:
                'Quando marcado apresentará a posição do(a) aluno(a) no protocolo (inscrição e consulta)',
              value: process.value.showPriorityProtocol,
              rules: '',
              containerClass: 'col-12',
            },
            {
              type: 'CHECKBOX',
              name: 'showWaitingList',
              label:
                'Apresentar consulta da lista de espera do processo na tela inicial',
              subLabel:
                'Quando marcado, divulgará publicamente a lista de espera e critérios conforme a Lei 14.685/2023',
              value: process.value.showWaitingList,
              rules: '',
              containerClass: 'col-12',
            },
            {
              type: 'RICH_TEXT',
              label: 'Critérios utilizados para priorização do processo',
              placeholder:
                'Descreva quais foram os critérios para que sejam exibidos junto com a publicação da lista de espera a Lei 14.685/2023',
              name: 'criteria',
              containerClass: 'col-12',
              value: process.value.criteria,
              rules: '',
            },
            {
              type: 'CHECKBOX',
              name: 'forceSuggestedGrade',
              label:
                'Preencher automaticamente a série com base na data corte e faixa etária do(a) aluno(a)',
              // eslint-disable-next-line max-len
              subLabel:
                'Quando marcado o campo série será preenchido automaticamente com base na data corte e data de nascimento do(a) aluno(a)',
              value: process.value.forceSuggestedGrade,
              rules: '',
              containerClass: 'col-12',
            },
            {
              type: 'CHECKBOX',
              name: 'allowResponsibleSelectMapAddress',
              label: 'Permitir o(a) responsável selecionar no mapa o endereço',
              subLabel:
                'Quando marcado o(a) responsável poderá selecionar o endereço manualmente pelo mapa',
              value: process.value.allowResponsibleSelectMapAddress,
              rules: '',
              containerClass: 'col-12',
            },
            {
              type: 'CHECKBOX',
              name: 'blockIncompatibleAgeGroup',
              label:
                'Bloquear inscrição quando a idade do(a) aluno(a) não for compatível com a faixa etária das séries ofertadas',
              subLabel:
                'Quando marcado não permitirá o(a) responsável concluir a inscrição caso a idade do(a) aluno(a) não seja compatível com a faixa etária de nenhuma das séries disponíveis no processo',
              value: process.value.blockIncompatibleAgeGroup,
              rules: '',
              containerClass: 'col-12',
            },
            {
              type: 'CHECKBOX',
              name: 'onePerYear',
              label: 'Permitir apenas uma inscrição por ano',
              subLabel:
                'Quando marcado não irá permitir que o aluno tenha mais de uma inscrição em aberto durante o ano do processo',
              value: process.value.blockIncompatibleAgeGroup,
              rules: '',
              containerClass: 'col-12',
            },
            {
              type: 'CHECKBOX',
              name: 'autoRejectByDays',
              label: 'Indeferir automaticamente inscrições por prazo',
              subLabel:
                'Quando marcado irá indeferir inscrições que tenham superado o número de dias na situação "Em convocação"',
              value: process.value.autoRejectByDays,
              rules: '',
              containerClass: 'col-12',
            },
            {
              type: 'NUMBER',
              label: 'Número de dias para o indeferimento',
              name: 'autoRejectDays',
              rules: 'max_value:99',
              value: process.value.autoRejectDays,
              containerClass: 'col-12',
            },
            {
              type: 'CHECKBOX',
              name: 'selectedSchools',
              label: 'Selecionar as escolas que farão parte do processo',
              subLabel:
                'Quando marcado apenas as escolas selecionas terão suas vagas disponibilizadas no processo',
              value: process.value.selectedSchools,
              rules: '',
              containerClass: 'col-12',
            },
          ],
          buttons: [
            {
              type: 'button',
              label: 'Voltar',
              class: 'btn btn-block btn-outline-light',
              containerClass: 'offset-md-3 col-md-3',
              block: true,
              action: handleClickBack,
              bind: {
                'data-test': 'back-button',
              },
            },
            {
              type: 'submit',
              label: 'Prosseguir',
              class: 'btn btn-block btn-primary',
              containerClass: 'col-md-3 mt-3 mt-md-0',
              block: true,
              bind: {
                'data-test': 'submit-button',
              },
            },
          ],
          buttonsContainer: {
            class: 'row mt-5',
          },
        } as unknown as FormSchema)
    );

    const submit = (model: ProcessCreate) => {
      loaderSubmit(() =>
        ProcessApi.post({
          ...process.value,
          ...model,
          waitingListLimit: Number(model.waitingListLimit),
          autoRejectDays: model.autoRejectByDays
            ? Number(model.autoRejectDays)
            : null,
          minimumAge: model.minimumAge ? Number(model.minimumAge) : null,
          schools: model.selectedSchools ? model.schools : [],
          grouper: model.grouper ?? null,
          id: process.value.id ?? null,
        })
      ).then((res) => {
        router.push({
          name: 'process.fields',
          params: {
            id: res.id,
            newProcess: String(!props.id),
          },
        });
      });
    };

    const handleClickBack = () => {
      router.push({
        name: 'process.show',
      });
    };

    const getProcess = async () => {
      if (!props.id) return;

      loaderProcess(() =>
        ProcessApi.listCreate({
          id: props.id as string,
        })
      ).then((res) => {
        if (props.newProcess === 'false') {
          pageview({
            path: `/processos/${res.name
              ?.toLowerCase()
              .replaceAll(' ', '-')}/editar`,
          });
        }
      });
    };

    const getInitialData = () => {
      loader(ProcessApi.listCourses).then(
        ({
          courses: coursesResponse,
          periods: periodsResponse,
          schoolYears: schoolYearsResponse,
          schools: schoolsResponse,
          groupers: groupersResponse,
        }) => {
          courses.value = coursesResponse
            .sort((a, b) => (a.name > b.name ? 1 : -1))
            .map((course) => ({
              ...course,
              grades: course.grades.sort((a, b) => (a.name > b.name ? 1 : -1)),
            }));

          periods.value = periodsResponse.sort((a, b) =>
            a.name > b.name ? 1 : -1
          );

          schoolYears.value = schoolYearsResponse.sort((a, b) =>
            a.label > b.label ? 1 : -1
          );

          schools.value = schoolsResponse;

          groupers.value = groupersResponse;
          groupers.value?.unshift({
            value: null,
            label: 'Sem grupo',
          });

          if (props.newProcess === 'true' || !props.id) {
            pageview({
              path: '/processos/criar-novo',
            });
          }
        }
      );
    };

    const loadingContent = computed(
      () => loadingProcess.value || loadingProcessData.value
    );

    getProcess();
    getInitialData();

    return {
      schema,
      submit,
      loadingContent,
      loading,
      process,
      courses,
      periods,
      schoolYears,
      schools,
      groupers,
    };
  },
});
</script>
