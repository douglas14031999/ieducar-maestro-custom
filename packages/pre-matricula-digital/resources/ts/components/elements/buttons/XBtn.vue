<script lang="ts">
import { Transition, computed, defineComponent, h, ref } from 'vue';
import useBtn, { useBtnProps } from '@/composables/useBtn';
import XIcon from '@/components/elements/icons/XIcon.vue';
import XSpinner from '@/components/loaders/XSpinner.vue';
import { hMergeSlot } from '@/utils/render';
export default defineComponent({
  props: {
    ...useBtnProps,
    loadingNormal: Boolean,
  },
  emits: ['click'],
  setup(props, { slots, emit }) {
    const { classes, style, innerClasses, attributes } = useBtn(props);
    const rootRef = ref('btn');
    const hasLabel = computed(
      () =>
        props.label !== undefined && props.label !== null && props.label !== ''
    );
    function onClick(e: MouseEvent) {
      if (e !== void 0) {
        if (e.defaultPrevented === true) return;
        if (props.loading === true) return;
        emit('click', e);
      }
    }
    const nodeProps = computed(() => ({
      ref: rootRef,
      class: `x-btn x-btn-item non-selectable no-outline ${classes.value}`,
      style: style.value,
      onClick,
      ...attributes.value,
    }));
    return () => {
      let inner = [];
      if (props.icon !== undefined) {
        inner.push(
          h(XIcon, {
            name: props.icon,
            left: props.stack === false && hasLabel.value === true,
            role: 'img',
            'aria-hidden': 'true',
            style: 'margin-top: 1px;',
          })
        );
      }
      if (hasLabel.value === true) {
        inner.push(h('span', { class: 'block' }, [props.label]));
      }
      inner = hMergeSlot(slots, inner);
      if (props.iconRight !== undefined && props.round === false) {
        inner.push(
          h(XIcon, {
            name: props.iconRight,
            right: props.stack === false && hasLabel.value === true,
            role: 'img',
            'aria-hidden': 'true',
            style: 'margin-top: 1px;',
          })
        );
      }
      const child = [];
      child.push(
        h(
          'span',
          {
            class: `x-btn__content text-center ${innerClasses.value}`,
          },
          inner
        )
      );
      if (props.loading !== void 0) {
        child.push(
          h(
            Transition,
            {
              name: 'x-transition--fade',
            },
            () =>
              props.loading === true
                ? [
                    h(
                      'span',
                      {
                        key: 'loading',
                        class:
                          'absolute-full d-flex justify-content-center align-items-center',
                      },
                      slots.loading !== void 0
                        ? slots.loading()
                        : [
                            h(XSpinner, {
                              normal: props.loadingNormal,
                              size: 'sm',
                              color: props.outline
                                ? props.textColor || props.color || 'white'
                                : 'white',
                            }),
                          ]
                    ),
                  ]
                : null
          )
        );
      }
      return h('button', nodeProps.value, child);
    };
  },
});
</script>
