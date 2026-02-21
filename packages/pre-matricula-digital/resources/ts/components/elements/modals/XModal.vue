<template>
  <teleport v-if="portalIsActive" to="body">
    <div v-bind="$attrs" :class="rootClasses">
      <transition name="x-transition--fade" :appear="true">
        <div
          v-if="useBackdrop"
          class="x-modal__backdrop fixed-full"
          :aria-hidden="true"
          @click="onBackdropClick"
        ></div>
      </transition>
      <transition name="x-transition--fade" :appear="true">
        <div v-if="showing" :class="classes" tabindex="-1">
          <slot name="default"></slot>
        </div>
      </transition>
    </div>
  </teleport>
</template>

<script setup lang="ts">
import {
  computed,
  onBeforeUnmount,
  onMounted,
  ref,
  useAttrs,
  watch,
} from 'vue';
import { usePortal } from '@/composables';

let openedModals = 0;

const positionClass = {
  standard: 'fixed-full justify-content-center align-items-center',
  top: 'fixed-top justify-content-center',
  bottom: 'fixed-bottom justify-content-center',
  right: 'fixed-right align-items-center',
  left: 'fixed-left align-items-center',
};

const attrs = useAttrs();

const emit = defineEmits<{
  (action: 'onUpdate:modelValue', payload: boolean): void;
  (action: 'update:modelValue', payload: boolean): void;
}>();

const props = withDefaults(
  defineProps<{
    modelValue: boolean;
    // eslint-disable-next-line vue/prop-name-casing
    'onUpdate:modelValue'?: void;
    disable?: boolean;
    position?: keyof typeof positionClass;
    persistent?: boolean;
    fullWidth?: boolean;
    fullHeight?: boolean;
    square?: boolean;
    maximized?: boolean;
    seamless?: boolean;
  }>(),
  {
    'onUpdate:modelValue': undefined,
    modelValue: false,
    disable: false,
    position: 'standard',
    persistent: false,
    fullWidth: false,
    fullHeight: false,
    square: false,
    maximized: false,
    seamless: false,
  }
);

const showing = ref(false);

const classes = computed(() => ({
  'x-modal__inner': true,
  'd-flex': true,
  'no-pointer-events': true,
  'x-modal__inner--maximized': props.maximized === true,
  'x-modal__inner--minimized': props.maximized === false,
  'x-modal__inner--standard': true,
  'x-modal__inner--top': props.position === 'top',
  'x-modal__inner--bottom': props.position === 'bottom',
  'x-modal__inner--right': props.position === 'right',
  'x-modal__inner--left': props.position === 'left',
  [positionClass[props.position]]: true,
  'x-modal__inner--fullwidth': props.fullWidth === true,
  'x-modal__inner--fullheight': props.fullHeight === true,
  'x-modal__inner--square': props.square === true,
}));

const useBackdrop = computed(
  () => showing.value === true && props.seamless !== true
);

const addEscapeKey = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    hide();
  }
};

const handleShow = () => {
  if (openedModals < 1 && useBackdrop.value === true) {
    document.body.classList.add('x-body--modal');
    document.getElementById('pmd_navbar')?.classList.add('x-nav--modal');
  }
  openedModals += 1;
  document.addEventListener('keydown', addEscapeKey, true);
};

const handleHide = () => {
  if (openedModals < 2) {
    document.body.classList.remove('x-body--modal');
    document.getElementById('pmd_navbar')?.classList.remove('x-nav--modal');
  }
  openedModals = openedModals - 1;
  document.removeEventListener('keydown', addEscapeKey, true);
};

const processHide = () => {
  if (showing.value === false) {
    return;
  }

  showing.value = false;
  handleHide();
};

const processShow = () => {
  if (showing.value === true) {
    return;
  }

  showing.value = true;
  handleShow();
};

const hide = () => {
  if (props.disable === true) {
    return;
  }

  processHide();
};

const onBackdropClick = () => {
  if (props.persistent !== true) {
    hide();
  }
};

const rootClasses = computed(() => [
  'x-modal fullscreen no-pointer-events',
  attrs.class,
]);

const { showPortal, hidePortal, portalIsActive } = usePortal();

function processModelChange(val?: boolean) {
  if (
    props.disable === true &&
    val === true &&
    props['onUpdate:modelValue'] !== void 0
  ) {
    emit('update:modelValue', false);
  } else if ((val === true) !== showing.value) {
    if (val === true) {
      processShow();
    } else {
      processHide();
    }
  }
}

watch(showing, (val) => {
  if (val === true) showPortal();
  else if (val === false) {
    emit('update:modelValue', false);
    hidePortal();
  }
});

watch(() => props.modelValue, processModelChange);

onBeforeUnmount(() => {
  processModelChange(false);
});

onMounted(() => {
  processModelChange(props.modelValue);
});

defineExpose({
  rootClasses,
  useBackdrop,
  onBackdropClick,
  showing,
  classes,
  portalIsActive,
});
</script>
