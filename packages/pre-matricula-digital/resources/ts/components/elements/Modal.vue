<template>
  <x-modal
    v-model="open"
    :maximized="maximized"
    :seamless="seamless"
    :persistent="persistent"
    :full-width="fullWidth"
  >
    <template #default>
      <x-card
        bordered
        :class="[
          { 'overflow-visible': isOverflowVisible || overflowVisible },
          large ? 'mw-960' : 'mw-440',
        ]"
      >
        <x-card-section v-if="!initialLoading">
          <slot name="header">
            <div class="d-flex justify-content-between">
              <div class="flex-grow-1 modal-title">
                <div class="d-flex justify-content-start">
                  <div
                    v-if="!!iconLeft"
                    v-tooltip.bottom-start="tooltipIcon || null"
                    :class="iconLeft"
                    class="align-self-center mr-3 mt-1"
                  />
                  <h4 :class="titleClass" class="m-0" v-html="title" />
                  <div
                    v-if="!!iconRight"
                    v-tooltip.bottom-start="tooltipIcon || null"
                    :class="iconRight"
                    class="align-self-center ml-3 mt-1"
                  />
                </div>
              </div>
              <div class="d-flex align-items-center">
                <x-btn
                  v-tooltip.bottom="'Fechar'"
                  fab-mini
                  flat
                  dense
                  icon="mdi-close"
                  color="primary"
                  class="flex-row justify-content-center"
                  @click="close"
                />
              </div>
            </div>
          </slot>
        </x-card-section>

        <hr v-if="!initialLoading" class="m-0" />

        <x-card-section
          v-if="!initialLoading"
          class="pt-2 pb-2 custom-modal-body"
          :class="{
            'overflow-visible': isOverflowVisible || overflowVisible,
          }"
        >
          <slot name="prompt" />
          <slot name="body" />
        </x-card-section>

        <hr v-if="!noFooter && !initialLoading" class="m-0" />

        <x-card-section v-if="!initialLoading" class="pt-2 pb-2">
          <slot name="footer" />
        </x-card-section>

        <skeleton-modal
          v-if="initialLoading"
          :body-class="bodyClass"
          :loading-inputs="loadingInputs"
          :loading-text="loadingText"
          :no-footer="noFooter"
          :style="large ? 'min-width: 960px;' : 'min-width: 440px;'"
        />
      </x-card>
    </template>
  </x-modal>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import SkeletonModal from '@/components/loaders/components/Modal.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XModal from '@/components/elements/modals/XModal.vue';
import { useVModel } from '@vueuse/core';

const props = withDefaults(
  defineProps<{
    large?: boolean;
    modelValue?: boolean;
    title: string;
    titleClass?: string;
    bodyClass?: string;
    position?: string;
    noHeader?: boolean;
    noFooter?: boolean;
    overflowVisible?: boolean;
    initialLoading?: boolean;
    iconLeft?: string;
    iconRight?: string;
    loadingInputs?: boolean;
    loadingText?: boolean;
    tooltipIcon?: string;
    isOverflowVisible?: boolean;
    seamless?: boolean;
    persistent?: boolean;
    maximized?: boolean;
    fullWidth?: boolean;
  }>(),
  {
    large: false,
    modelValue: false,
    title: '',
    titleClass: '',
    bodyClass: '',
    position: 'standard',
    noHeader: false,
    noFooter: false,
    overflowVisible: false,
    initialLoading: false,
    iconLeft: '',
    iconRight: '',
    loadingInputs: false,
    loadingText: false,
    tooltipIcon: '',
    isOverflowVisible: false,
    seamless: false,
    persistent: false,
    maximized: false,
    fullWidth: false,
  }
);

const emit = defineEmits<{
  (action: 'closeModal'): void;
  (action: 'update:modelValue', payload: boolean): void;
}>();

const overflowVisible = ref(false);

const open = useVModel(props, 'modelValue');

const close = async () => {
  emit('closeModal');
  open.value = false;
};

const show = () => {
  open.value = true;
};

const setOverflowVisible = (val: boolean) => {
  overflowVisible.value = val;
};

const classes = computed(() => 'fullscreen');

defineExpose({
  setOverflowVisible,
  close,
  show,
  classes,
});
</script>

<script lang="ts">
export default {
  inheritAttrs: false,
};
</script>
