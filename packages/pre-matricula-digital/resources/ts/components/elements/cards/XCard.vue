<script lang="ts">
import { computed, defineComponent, h } from 'vue';
import { hSlot } from '@/utils/render';
export default defineComponent({
  name: 'XCard',
  props: {
    tag: {
      type: String,
      default: 'div',
    },
    square: Boolean,
    flat: Boolean,
    bordered: Boolean,
    hoverable: Boolean,
  },
  setup(props, { slots }) {
    /* eslint-disable prefer-template */
    const classes = computed(
      () =>
        'x-card' +
        (props.bordered === true ? ' x-card--bordered' : '') +
        (props.hoverable === true ? ' x-card--hover' : '') +
        (props.square === true ? ' x-card--square no-border-radius' : '') +
        (props.flat === true ? ' x-card--flat no-shadow' : '')
    );
    return () => h(props.tag, { class: classes.value }, hSlot(slots));
  },
});
</script>
