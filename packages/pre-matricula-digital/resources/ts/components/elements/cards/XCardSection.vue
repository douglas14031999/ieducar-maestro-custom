<script lang="ts">
import { computed, defineComponent, h } from 'vue';
import { hSlot } from '@/utils/render';
export default defineComponent({
  name: 'XCardSection',
  props: {
    tag: {
      type: String,
      default: 'div',
    },
    horizontal: Boolean,
  },
  setup(props, { slots }) {
    /* eslint-disable prefer-template */
    const classes = computed(
      () =>
        'x-card__section' +
        ` x-card__section--${
          props.horizontal === true ? 'horiz row no-wrap' : 'vert'
        }`
    );
    return () => h(props.tag, { class: classes.value }, hSlot(slots));
  },
});
</script>
