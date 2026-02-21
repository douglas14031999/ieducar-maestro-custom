import { computed } from 'vue';

interface IAlignMap {
  left: string;
  center: string;
  right: string;
  between: string;
  around: string;
  evenly: string;
  stretch: string;
}

export const alignMap: IAlignMap = {
  left: 'start',
  center: 'center',
  right: 'end',
  between: 'between',
  around: 'around',
  evenly: 'evenly',
  stretch: 'stretch',
};

export const alignValues = Object.keys(alignMap);

export const useAlignProps = {
  align: {
    type: String,
    validator: (v: string) => alignValues.includes(v),
  },
};

// eslint-disable-next-line func-names
export default function (props: Record<string, unknown>) {
  return computed(() => {
    // eslint-disable-next-line
    const align =
      props.align === void 0
        ? props.vertical === true
          ? 'stretch'
          : 'left'
        : props.align;

    return `d-flex ${
      props.vertical === true ? 'align-items' : 'justify-content'
    }-${alignMap[align as keyof IAlignMap]}`;
  });
}
