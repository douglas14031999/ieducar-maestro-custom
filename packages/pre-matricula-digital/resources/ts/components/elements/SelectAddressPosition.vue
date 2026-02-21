<template>
  <div class="col-12 mt-3 mb-3">
    <div class="row">
      <div v-if="adjustAddress" class="col-12">
        <div class="alert alert-warning d-flex flex-column gap-3">
          <div class="d-flex gap-2">
            <x-icon name="mdi-information-outline" />
            <strong>Algumas dicas:</strong>
          </div>
          <ul class="m-0 p-l-start-4-68">
            <li>
              Selecione o ícone de localização em vermelho presente no mapa
              abaixo
            </li>
            <li>
              Ao selecionar, clique e arraste o ícone para a localização de sua
              preferência.
            </li>
          </ul>
        </div>
      </div>
      <slot
        name="map"
        :adjust-address="adjustAddress"
        :set-new-position="handleNewPosition"
      ></slot>
      <div
        v-if="haveOldAddressPosition"
        class="d-flex flex-column gap-3 w-100 p-3"
      >
        <div class="alert alert-success d-flex flex-column gap-3">
          <div class="d-flex gap-2">
            <x-icon name="mdi-check-circle-outline" />
            <strong>Sua localização foi alterada com sucesso!</strong>
          </div>
          <div class="d-flex flex-column gap-3">
            Caso você tenha selecionado a localização errada, você pode clicar
            no botão abaixo para voltar para a localização original.
            <div>
              <x-btn
                class="bg-white text-danger border-danger"
                no-caps
                size="md"
                @click="undoAddressPosition"
              >
                Desfazer alteração
              </x-btn>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="!haveOldAddressPosition && allowResponsibleSelectMapAddress"
        class="d-flex flex-column gap-3 w-100"
        :class="{
          'px-3 py-1': !adjustAddress,
          'p-3': adjustAddress,
        }"
      >
        <div class="d-flex flex-row-reverse w-100 gap-3">
          <x-btn
            v-if="adjustAddress"
            label="Confirmar localização"
            color="primary"
            outline
            no-caps
            no-wrap
            class="w-50"
            @click="confirmNewLocation"
          />
          <x-btn
            v-if="adjustAddress"
            label="Cancelar localização"
            color="gray-500"
            class="w-50"
            outline
            no-caps
            no-wrap
            @click="cancelNewLocation"
          />
          <span
            v-else
            class="font-muli font-weight-bold text-gray-600 text-size-small"
          >
            Esta não é a sua localização?
            <span class="text-primary clickable" @click="setNewLocation"
              >Ajuste aqui</span
            >
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { AddressPosition, LatLng } from '@/types';
import { computed, ref } from 'vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XIcon from '@/components/elements/icons/XIcon.vue';
import { newLocationPosition } from '@/util';
import { useVModel } from '@vueuse/core';

const emit = defineEmits<{
  (action: 'update:old', value: AddressPosition): void;
  (action: 'update:adjusted', value: AddressPosition): void;
  (action: 'undoAddressPosition'): void;
  (action: 'confirmNewLocation'): void;
  (action: 'setNewLocation'): void;
  (action: 'cancelNewLocation'): void;
}>();

const props = withDefaults(
  defineProps<{
    allowResponsibleSelectMapAddress: boolean;
    isUsingSecondAddress: boolean;
    old: AddressPosition;
    adjusted: AddressPosition;
  }>(),
  {
    allowResponsibleSelectMapAddress: false,
    isUsingSecondAddress: false,
    old: () => ({
      secondAddress: false,
      lat: null,
      lng: null,
    }),
    adjusted: () => ({
      secondAddress: false,
      lat: null,
      lng: null,
    }),
  }
);

const adjustAddress = ref(false);

const haveOldAddressPosition = computed(() => {
  return (
    Boolean(oldAddressPosition.value.lat) &&
    Boolean(oldAddressPosition.value.lng)
  );
});

const oldAddressPosition = useVModel(props, 'old');

const adjustedAddressPostition = useVModel(props, 'adjusted');

const setAdjustedAddressPositionNull = () => {
  adjustedAddressPostition.value = newLocationPosition();
};

const undoAddressPosition = () => {
  emit('undoAddressPosition');
  setAdjustedAddressPositionNull();
  oldAddressPosition.value = {
    ...newLocationPosition(),
    secondAddress: oldAddressPosition.value.secondAddress,
  };
};

const confirmNewLocation = () => {
  adjustAddress.value = false;

  emit('confirmNewLocation');
};

const setNewLocation = () => {
  adjustAddress.value = true;
  setAdjustedAddressPositionNull();
  emit('setNewLocation');
};

const cancelNewLocation = () => {
  adjustAddress.value = false;
  setAdjustedAddressPositionNull();
  emit('cancelNewLocation');
};

const handleNewPosition = (position: LatLng) => {
  adjustedAddressPostition.value = {
    ...position,
    secondAddress: props.isUsingSecondAddress,
  };
};
</script>
