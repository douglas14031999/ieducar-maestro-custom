import {
  App,
  AppContext,
  VNode,
  VNodeArrayChildren,
  createApp,
  getCurrentInstance,
  h,
  ref,
} from 'vue';
import { FormButton } from '@/types';
import Modal from '@/components/elements/Modal.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XField from '@/components/x-form/XField.vue';

interface Payload {
  title: string;
  titleClass?: string;
  subTitle?: string;
  description?: string;
  position?: string;
  iconLeft?: string;
  iconRight?: string;
  maximized?: boolean;
  seamless?: boolean;
  persistent?: boolean;
  fullWidth?: boolean;
  prompt?: boolean;
  buttons?: FormButton[];
  confirm?: boolean;
  onOk?: (model?: string) => void;
  onCancel?: () => void;
}

type RawChildren =
  | string
  | number
  | boolean
  | VNode
  | VNodeArrayChildren
  | (() => unknown);

const createChildApp = (
  appConfig: { setup: () => typeof h },
  parentApp: AppContext
) => {
  let app: App<Element> | undefined = createApp(appConfig);
  app.config.globalProperties = parentApp.config.globalProperties;
  const { ...appContext } = parentApp.app._context;
  Object.assign(app._context, appContext);

  app.mount(document.body.appendChild(document.createElement('div')));

  return () => {
    app?.unmount();
    app = undefined;
  };
};

export function useModal(appContext?: AppContext) {
  const _appContext = appContext || getCurrentInstance()?.appContext;
  const input = ref<string>('');
  const instance = _appContext;
  const model = ref(false);
  let app: () => void;

  const globalDialog = (parentApp: AppContext, payload: Payload) => {
    const dialogRef = ref<InstanceType<typeof Modal> | null>(null);

    app = createChildApp(
      {
        setup: () => () => {
          return h(
            Modal,
            {
              title: payload.title,
              titleClass: `text-${payload.titleClass || 'primary'}`,
              maximized: payload.maximized,
              position: payload.position,
              iconLeft: payload.iconLeft,
              iconRight: payload.iconRight,
              seamless: payload.seamless,
              persistent: payload.persistent,
              fullWidth: payload.fullWidth,
              noFooter: true,
              ref: dialogRef,
              modelValue: model.value,
              'onUpdate:modelValue': handleCloseModal,
            },
            {
              body: () =>
                h('div', {
                  class: 'text-justify',
                  innerHTML: payload.description,
                }),
              prompt: () => constructorPrompt(payload),
              footer: () => constructFooter(payload),
            }
          );
        },
      },
      parentApp as AppContext
    );
  };

  const dialog = (payload: Payload) => {
    app?.();
    model.value = true;
    return globalDialog(instance as AppContext, payload);
  };

  const constructorPrompt = (payload: Payload) => {
    if (payload.prompt) {
      return h(
        'div',
        {
          class: 'd-flex justify-content-between',
        },
        [
          h(XField, {
            value: input.value,
            name: 'text',
            type: 'TEXT',
            onInput: (e: Event) => {
              const target = e.target as HTMLInputElement;
              input.value = target.value;
            },
          }),
        ]
      );
    }
  };

  const constructFooter = (payload: Payload) => {
    if (payload.buttons) {
      const buttons: RawChildren = [];
      payload.buttons.forEach((button) => {
        buttons.push(
          h(XBtn, {
            type: button.type,
            label: button.label,
            class: button.class,
            containerClass: button.containerClass,
            block: button.block,
            outline: button.outline,
            noCaps: button.noCaps,
            noWrap: button.noWrap,
            onClick: () => {
              if (button.action) {
                button.action();
              }

              handleCloseModal(false);
            },
          })
        );
      });
      return h(
        'div',
        {
          class: 'd-flex justify-content-between',
        },
        buttons
      );
    }

    if (payload.confirm) {
      return h(
        'div',
        {
          class: 'd-flex gap-2',
        },
        [
          h(XBtn, {
            type: 'button',
            label: 'Cancelar',
            color: 'gray-500',
            outline: true,
            class: 'w-50',
            containerClass: '',
            block: false,
            noCaps: true,
            noWrap: true,
            onClick: () => {
              if (payload.onCancel) {
                payload.onCancel();
              }

              handleCloseModal(false);
            },
          }),
          h(XBtn, {
            type: 'button',
            label: 'Prosseguir',
            color: 'primary',
            class: 'w-50',
            containerClass: '',
            block: false,
            noCaps: true,
            noWrap: true,
            onClick: () => {
              if (payload.onOk) {
                payload.onOk(input.value);
              }

              handleCloseModal(false);
            },
          }),
        ]
      );
    }

    return h(
      'div',
      {
        class: 'd-flex justify-content-end',
      },
      [
        h(XBtn, {
          type: 'button',
          label: 'Entendi',
          class: 'w-100',
          color: 'primary',
          containerClass: '',
          block: true,
          noCaps: true,
          noWrap: true,
          onClick: () => {
            if (payload.onOk) {
              payload.onOk(input.value);
            }

            handleCloseModal(false);
          },
        }),
      ]
    );
  };

  const handleCloseModal = (val: boolean) => {
    model.value = val;

    app?.();
  };

  return { dialog };
}
