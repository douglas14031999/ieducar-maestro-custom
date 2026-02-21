<script lang="ts">
import { computed, defineComponent, h } from 'vue';
import { hSlot } from '@/utils/render';

export default defineComponent({
  name: 'Skeleton',
  props: {
    tag: {
      type: String,
      default: 'div',
    },
    type: {
      type: String,
      default: 'rect',
    },
    animation: {
      type: String,
      default: 'wave',
    },
    square: Boolean,
    bordered: Boolean,
    size: {
      type: String,
      default: null,
    },
    width: {
      type: String,
      default: null,
    },
    height: {
      type: String,
      default: null,
    },
  },
  setup(props, { slots }) {
    const style = computed(() =>
      props.size !== null
        ? { width: props.size, height: props.size }
        : { width: props.width, height: props.height }
    );
    const classes = computed(
      () =>
        `x-skeleton x-skeleton--light x-skeleton--type-${props.type}` +
        (props.animation !== 'none'
          ? ` x-skeleton--anim x-skeleton--anim-${props.animation}`
          : '') +
        (props.square === true ? ' x-skeleton--square' : '') +
        (props.bordered === true ? ' x-skeleton--bordered' : '')
    );
    return () =>
      h(
        props.tag,
        {
          class: classes.value,
          style: style.value,
        },
        hSlot(slots)
      );
  },
});
</script>
