<script lang="ts">
import { VNode, computed, defineComponent, h } from 'vue';
import { hMergeSlot, hSlot } from '@/utils/render';
import useSize, { useSizeProps } from '@/composables/useSize';
const libMap = {
  'fa-': (i: string) => `fa ${i}`,
  'mdi-': (i: string) => `mdi ${i}`,
  'pmd-': (i: string) => `icon-${i}`,
};
const libRE = new RegExp(`^(${Object.keys(libMap).join('|')})`);
const faLaRE = /^[l|f]a[s|r|l|b|d]? /;
export default defineComponent({
  name: 'Icon',
  props: {
    ...useSizeProps,
    tag: {
      type: String,
      default: 'i',
    },
    name: {
      type: String,
      default: '',
    },
    color: {
      type: String,
      default: '',
    },
    left: Boolean,
    right: Boolean,
  },
  setup(props, { slots }) {
    const sizeStyle = useSize(props);
    const classes = computed(
      () =>
        // eslint-disable-next-line
        'pmd-icon' +
        (props.left === true ? ' on-left' : '') +
        (props.right === true ? ' on-right' : '') +
        // eslint-disable-next-line no-void
        (props.color !== void 0 ? ` text-${props.color}` : '')
    );
    const type = computed(() => {
      let cls;
      const icon = props.name;
      if (!icon) {
        return {
          none: true,
          cls: classes.value,
        };
      }
      const content = ' ';
      const matches = (icon.match(libRE) as string[])[1] as keyof typeof libMap;
      if (matches !== null) {
        cls = libMap[matches](icon);
      } else if (faLaRE.test(icon) === true) {
        cls = icon;
      }
      return {
        cls: `${cls} ${classes.value}`,
        content,
      };
    });
    return () => {
      const data = {
        class: type.value.cls,
        style: sizeStyle.value,
        'aria-hidden': 'true',
        role: 'presentation',
      };
      if (type.value.none === true) {
        return h(props.tag, data, hSlot(slots));
      }
      return h(
        props.tag,
        data,
        hMergeSlot(slots, [type.value.content as unknown as VNode])
      );
    };
  },
});
</script>
