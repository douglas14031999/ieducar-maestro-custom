<template>
  <div>
    <h2 class="font-muli-20-primary">
      Preencha o formulário abaixo para criar sua inscrição:
    </h2>
    <p>
      Preencha os campos abaixo para identificar os dados do(a) responsável pela
      inscrição do(a) aluno(a). Os campos com asterisco (*) são de preenchimento
      <strong>obrigatório</strong> para prosseguir com a inscrição.
    </p>
    <hr class="mt-5 mb-5" />
    <div class="mb-3 d-sm-flex justify-content-between">
      <h3 class="font-hind-18-primary">
        Dados do(a) responsável pelo(a) aluno(a)
      </h3>
      <x-btn
        label="Limpar dados"
        class="border-danger text-danger flex-row"
        no-caps
        no-wrap
        @click="$emit('clean')"
      />
    </div>
    <fields
      v-model:fields="modelFields"
      v-model:data="modelResponsible"
      :errors="errors"
    />
    <p class="mt-4 mb-4">Vínculo com o(a) aluno(a)</p>
    <div class="row">
      <x-field
        v-model="modelResponsible['relationType']"
        container-class="col-12 col-md-6 form-group"
        rules="required"
        label="Relação com o(a) aluno(a)"
        name="relationType"
        type="SELECT"
        :options="store.relationTypes"
        :errors="!!errors['relationType']"
      />
    </div>
    <p class="mt-4 mb-4">Endereço residencial</p>
    <p class="text-muted">
      Digite o <strong>CEP</strong> que corresponde ao seu endereço e clique em
      'Buscar' para preenchimento automático. Caso seu endereço não seja
      encontrado, preencha manualmente os campos necessários para identificá-lo
      (endereço, número, bairro).
    </p>
    <address-fields
      v-model:fetchingAddressLatLng="modelFetchingPrimaryAddressLatLng"
      v-model:data="modelResponsible.address"
      :set-field-value="setFieldValue"
      :errors="errors"
      name="address"
    />
    <div
      v-if="
        modelResponsible &&
        store.config.allow_optional_address &&
        !modelResponsible.useSecondAddress
      "
      class="row mt-5"
    >
      <div class="col-12 col-md-6 offset-md-3">
        <x-btn
          data-test="btn-use-second-address"
          label="Adicionar endereço secundário"
          color="primary"
          class="w-100"
          no-caps
          no-wrap
          @click="modelResponsible.useSecondAddress = true"
        />
      </div>
    </div>
    <template v-if="modelResponsible && modelResponsible.useSecondAddress">
      <p class="mt-4 mb-4">Endereço secundário</p>
      <address-fields
        v-model:data="modelResponsible.secondAddress"
        v-model:fetchingAddressLatLng="modelFetchingSecondaryAddressLatLng"
        :set-field-value="setFieldValue"
        :errors="errors"
        name="secondAddress"
      />
      <div class="row mt-5">
        <div class="col-12 col-md-6 offset-md-3">
          <x-btn
            data-test="btn-doenst-use-second-address"
            label="Não usar endereço secundário"
            class="border-danger text-danger w-100"
            no-caps
            no-wrap
            @click="doesntuseSecondAddress"
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script lang="ts">
import {
  ParseFieldFromProcess,
  PreRegistrationResponsibleField,
} from '../types';
import { PropType, defineComponent } from 'vue';
import AddressFields from '@/components/form/AddressFields.vue';
import { GenericFields } from '@/components/form/Fields.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XField from '@/components/x-form/XField.vue';
import { newAddress } from '@/util';
import { useGeneralStore } from '@/store/general';
import { useVModel } from '@vueuse/core';

export default defineComponent({
  components: {
    AddressFields,
    Fields: GenericFields<PreRegistrationResponsibleField>(),
    XBtn,
    XField,
  },
  props: {
    responsible: {
      type: Object as PropType<PreRegistrationResponsibleField>,
      default: () => ({}),
    },
    errors: {
      type: Object as PropType<{
        [key: string]: boolean;
      }>,
      default: () => ({}),
    },
    fields: {
      type: Array as PropType<ParseFieldFromProcess[]>,
      default: () => [],
    },
    fetchingPrimaryAddressLatLng: {
      type: Boolean as PropType<boolean>,
      default: false,
    },
    fetchingSecondaryAddressLatLng: {
      type: Boolean as PropType<boolean>,
      default: false,
    },
    setFieldValue: {
      type: Function as PropType<
        (field: string, value: string | number) => void
      >,
      required: false,
      default: () => ({}),
    },
  },
  emits: [
    'clean',
    'update:fetchingPrimaryAddressLatLng',
    'update:fetchingSecondaryAddressLatLng',
  ],
  setup(props) {
    const store = useGeneralStore();

    const modelFields = useVModel(props, 'fields');
    const modelResponsible = useVModel(props, 'responsible');
    const modelFetchingPrimaryAddressLatLng = useVModel(
      props,
      'fetchingPrimaryAddressLatLng'
    );
    const modelFetchingSecondaryAddressLatLng = useVModel(
      props,
      'fetchingSecondaryAddressLatLng'
    );

    const doesntuseSecondAddress = () => {
      modelResponsible.value.useSecondAddress = false;
      modelResponsible.value.secondAddress = newAddress();
    };

    return {
      store,
      modelFields,
      modelResponsible,
      modelFetchingPrimaryAddressLatLng,
      modelFetchingSecondaryAddressLatLng,
      doesntuseSecondAddress,
    };
  },
});
</script>
