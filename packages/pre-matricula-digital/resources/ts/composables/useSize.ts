import { computed } from 'vue';

export const useSizeDefaults = {
  xs: 15,
  sm: 20,
  md: 30,
  lg: 50,
  xl: 80,
};

export const useSizeProps = {
  size: String,
};

// eslint-disable-next-line
export default function (props: any, sizes = useSizeDefaults) {
  return computed(() =>
    // eslint-disable-next-line no-void
    props.size !== void 0
      ? {
          fontSize:
            props.size in sizes
              ? `${sizes[props.size as keyof typeof sizes]}px`
              : props.size,
        }
      : null
  );
}
