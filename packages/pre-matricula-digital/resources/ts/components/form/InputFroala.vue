<template>
  <vue-froala
    v-model="model"
    v-bind="$attrs"
    :tag="'textarea'"
    :config="configTotal"
  />
</template>

<script setup lang="ts">
import 'froala-editor/css/froala_editor.pkgd.min.css';
import 'froala-editor/js/plugins.pkgd.min';
import 'froala-editor/js/languages/pt_br';
import { Indexable, ModelValue } from '@/types';
import { computed, ref } from 'vue';
import VueFroala from '@/components/form/VueFroala/VueFroala.vue';
import { useVModel } from '@vueuse/core';

const props = withDefaults(
  defineProps<{
    data?: ModelValue;
    placeholder?: string;
    config?: Indexable;
    tag?: string;
    name: string;
    disabled?: boolean;
  }>(),
  {
    data: null,
    placeholder: 'Adicione sua Mensagem aqui',
    config: () => ({}),
    tag: 'textarea',
    disabled: false,
  }
);

const configDefault = ref({
  placeholderText: props.placeholder,
  charCounterCount: false,
  toolbarInline: false,
  pluginsEnabled: [
    'align',
    'codeView',
    'colors',
    'emoticons',
    'entities',
    'fontFamily',
    'fontSize',
    'fullscreen',
    'lineBreaker',
    'lineHeight',
    'link',
    'lists',
    'paragraphFormat',
    'paragraphStyle',
    'quote',
    'save',
    'url',
    'wordPaste',
    'print',
    'help',
  ],
  toolbarStickyOffset: 76,
  heightMin: 200,
  attribution: false,
  language: 'pt_br',
  toolbarButtonsXS: {
    moreText: {
      buttons: [
        'bold',
        'italic',
        'underline',
        'fontFamily',
        'fontSize',
        'lineHeight',
        'textColor',
        'backgroundColor',
        'specialCharacters',
      ],
      align: 'left',
      buttonsVisible: 2,
    },
    moreRich: {
      buttons: ['insertLink', 'insertHR'],
      align: 'left',
      buttonsVisible: 0,
    },
    moreParagraph: {
      buttons: [
        'formatUL',
        'formatOLSimple',
        'alignLeft',
        'alignCenter',
        'alignRight',
        'alignJustify',
      ],
      align: 'left',
      buttonsVisible: 0,
    },
    moreMisc: {
      buttons: ['undo', 'redo', 'emoticons', 'fullscreen', 'selectAll', 'help'],
      align: 'right',
      buttonsVisible: 2,
    },
  },
  toolbarButtons: {
    moreText: {
      buttons: [
        'bold',
        'italic',
        'underline',
        'fontFamily',
        'fontSize',
        'lineHeight',
        'textColor',
        'backgroundColor',
      ],
      align: 'left',
      buttonsVisible: 3,
    },
    moreParagraph: {
      buttons: [
        'formatOLSimple',
        'formatUL',
        'alignLeft',
        'alignCenter',
        'alignRight',
        'alignJustify',
      ],
      align: 'left',
      buttonsVisible: 2,
    },
    moreRich: {
      buttons: ['insertLink', 'insertHR'],
      align: 'left',
      buttonsVisible: 2,
    },
    moreOthers: {
      buttons: ['emoticons'],
      align: 'left',
      buttonsVisible: 1,
    },
    moreMisc: {
      buttons: ['undo', 'redo', 'fullscreen', 'print', 'selectAll', 'help'],
      align: 'right',
      buttonsVisible: 2,
    },
  },
  key: import.meta.env.VITE_FROALA_KEY,
  pasteAllowLocalImages: false,
  imagePaste: false,
  pasteDeniedTags: ['img'],
});

const configTotal = computed(() => ({
  ...configDefault.value,
  ...props.config,
}));

const model = useVModel(props, 'data');
</script>
