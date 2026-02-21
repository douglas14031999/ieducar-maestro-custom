<script lang="ts">
import { computed, defineComponent, h } from 'vue';
import { useSizeDefaults } from '@/composables/useSize';
function useSpinner(props: {
  size: number | string;
  color: string;
  normal: boolean;
}) {
  return {
    cSize: computed(() =>
      props.size in useSizeDefaults
        ? `${useSizeDefaults[props.size as keyof typeof useSizeDefaults]}px`
        : props.size
    ),
    classes: computed(() => {
      if (!props.normal) {
        return `x-spinner${props.color ? ` text-${props.color}` : ''}`;
      }
      return `x-normal-spinner-${props.size} text-${props.color}`;
    }),
  };
}
export default defineComponent({
  props: {
    size: {
      type: [Number, String],
      default: 'sm',
    },
    color: {
      type: String,
      default: 'white',
    },
    thickness: {
      type: Number,
      default: 5,
    },
    normal: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const { cSize, classes } = useSpinner(props);
    return !props.normal
      ? () =>
          h(
            'div',
            {
              class: 'd-flex',
            },
            [
              h(
                'svg',
                {
                  class: `${classes.value}
          x-spinner-mat
        `,
                  width: cSize.value,
                  height: cSize.value,
                  viewBox: '25 25 50 50',
                },
                [
                  h('circle', {
                    class: 'path',
                    cx: '50',
                    cy: '50',
                    r: '20',
                    fill: 'none',
                    stroke: 'currentColor',
                    'stroke-width': props.thickness,
                    'stroke-miterlimit': '10',
                  }),
                ]
              ),
            ]
          )
      : () =>
          h('div', {
            class: `${classes.value} x-normal-spinner d-flex`,
          });
  },
});
</script>
