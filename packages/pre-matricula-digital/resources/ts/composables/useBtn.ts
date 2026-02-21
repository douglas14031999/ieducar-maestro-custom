import useAlign, { useAlignProps } from '@/composables/useAlign';
import useSize, { useSizeProps } from '@/composables/useSize';
import { computed } from 'vue';

interface IAcc {
  tabindex: string;
  role: string;
  type?: string;
  disabled?: string;
  ['aria-disabled']?: string;
}

interface IPadding {
  none: number;
  xs: number;
  sm: number;
  md: number;
  lg: number;
  xl: number;
}

interface IDefaultSizes {
  xs: number;
  sm: number;
  md: number;
  lg: number;
  xl: number;
}

const padding: IPadding = {
  none: 0,
  xs: 4,
  sm: 8,
  md: 16,
  lg: 24,
  xl: 32,
};

const defaultSizes: IDefaultSizes = {
  xs: 8,
  sm: 10,
  md: 14,
  lg: 20,
  xl: 24,
};

export const useBtnProps = {
  ...useSizeProps,
  type: {
    type: String,
    default: 'button',
  },
  label: [Number, String],
  icon: String,
  iconRight: String,
  round: Boolean,
  outline: Boolean,
  flat: Boolean,
  unelevated: Boolean,
  rounded: Boolean,
  size: String,
  fab: Boolean,
  fabMini: Boolean,
  padding: String,
  color: String,
  textColor: String,
  noCaps: Boolean,
  noWrap: Boolean,
  dense: Boolean,
  tabindex: [Number, String],
  align: {
    ...useAlignProps.align,
    default: 'center',
  },
  stack: Boolean,
  stretch: Boolean,
  loading: {
    type: Boolean,
    default: null,
  },
  disable: Boolean,
};

// eslint-disable-next-line func-names
export default function (props: Record<string, unknown>) {
  const sizeStyle = useSize(props, defaultSizes);
  const alignClass = useAlign(props);

  const style = computed(() => {
    const obj =
      props.fab === false && props.fabMini === false ? sizeStyle.value : {};

    return props.padding !== void 0
      ? {
          ...obj,
          padding: (props.padding as string)
            ?.split(/\s+/)
            .map((v: string) =>
              v in padding ? `${padding[v as keyof IPadding]}px` : v
            )
            .join(' '),
          minWidth: '0',
          minHeight: '0',
        }
      : obj;
  });

  const isRounded = computed(
    () => props.rounded === true || props.fab === true || props.fabMini === true
  );

  const isActionable = computed(
    () => props.disable !== true && props.loading !== true
  );

  const tabIndex = computed(() =>
    isActionable.value === true ? (props.tabindex as string) || '0' : '-1'
  );

  const design = computed(() => {
    if (props.flat === true) return 'flat';
    if (props.outline === true) return 'outline';
    if (props.unelevated === true) return 'unelevated';
    return 'standard';
  });

  const attributes = computed(() => {
    const acc: IAcc = {
      tabindex: tabIndex.value,
      role: 'button',
    };

    if (props.type !== 'a') {
      acc.type = props.type as string;
    }

    if (props.disable === true) {
      acc.disabled = '';
      acc['aria-disabled'] = 'true';
    }

    return acc;
  });

  const classes = computed(() => {
    let colors;

    if (props.color !== void 0) {
      if (props.flat === true) {
        colors = `text-${props.textColor || props.color}`;
      } else if (props.outline === true) {
        colors = `text-${props.textColor || props.color} border-${
          props.textColor || props.color
        }`;
      } else {
        colors = `bg-${props.color} text-${props.textColor || 'white'}`;
      }
    } else if (props.textColor) {
      colors = `text-${props.textColor}`;
    }

    // eslint-disable-next-line
    const returnValue =
      `x-btn--${design.value} ` +
      // eslint-disable-next-line
      `x-btn--${
        props.round === true
          ? 'round'
          : `rectangle${isRounded.value === true ? ' x-btn--rounded' : ''}`
      }` +
      // eslint-disable-next-line
      (colors !== void 0 ? ' ' + colors : '') +
      // eslint-disable-next-line
      (isActionable.value === true
        ? ' x-btn--actionable pmd-focusable pmd-hoverable'
        : props.disable === true
        ? ' disabled'
        : '') +
      // eslint-disable-next-line
      (props.fab === true
        ? ' x-btn--fab'
        : props.fabMini === true
        ? ' x-btn--fab-mini'
        : '') +
      (props.noCaps === true ? ' x-btn--no-uppercase' : '') +
      (props.dense === true ? ' x-btn--dense' : '') +
      (props.stretch === true ? ' no-border-radius align-self-stretch' : '');

    return returnValue;
  });

  const innerClasses = computed(
    () =>
      alignClass.value +
      (props.stack === true ? ' flex-column' : '') +
      (props.noWrap === true ? ' no-wrap text-no-wrap' : '') +
      (props.loading === true ? ' x-btn__content--hidden' : '')
  );

  return {
    classes,
    style,
    innerClasses,
    attributes,
    isActionable,
  };
}
