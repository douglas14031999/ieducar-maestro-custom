<script lang="ts">
import {
  PropType,
  defineComponent,
  h,
  onMounted,
  ref,
  render,
  watch,
} from 'vue';
export default defineComponent({
  props: {
    tag: {
      type: String as PropType<string>,
      default: undefined,
    },
    value: {
      type: String as PropType<string>,
      default: null,
    },
  },
  setup(props) {
    const currentTag = ref('div');
    const f_element = ref();
    const element = ref();

    watch(
      [props.value],
      (newValue) => {
        f_element.value.innerHTML = newValue;
      },
      {
        deep: true,
      }
    );

    currentTag.value = props.tag || currentTag.value;

    onMounted(() => {
      f_element.value = element.value;
      if (props.value) {
        f_element.value.innerHTML = props.value;
      }
    });

    render(
      h(currentTag.value, {
        ref: element,
        class: 'fr-view',
      }),
      element.value
    );
  },
});
</script>
