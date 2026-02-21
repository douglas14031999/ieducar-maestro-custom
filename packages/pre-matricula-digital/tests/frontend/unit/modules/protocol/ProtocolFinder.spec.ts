import { VueWrapper, mount } from '@vue/test-utils';
import ProtocolFinder from '@/modules/protocol/components/ProtocolFinder.vue';
import { expect, vi } from 'vitest';

vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));

const spyPush = vi.fn();

const onMounted = async () => {
  const wrapper = mount(ProtocolFinder, {
    global: {
      plugins: [],
      stubs: [],
    },
  });

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('ProtocolFinder', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProtocolFinder>>;

  beforeEach(async () => {
    wrapper = await onMounted();

    await wrapper.vm.$nextTick();
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  test('the component exists', () => {
    expect(wrapper).toBeTruthy();
  });

  test('should match snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  test('check if protocol-pre-find components exists', () => {
    const component = wrapper.findComponent({
      name: 'protocol-pre-find',
    });

    expect(component).toBeTruthy();
  });

  test('when I click in "go back" button, it should go to "/"', async () => {
    const goBackButton = wrapper.find('[data-test="btn-back"]');

    goBackButton.trigger('click');

    await wrapper.vm.$nextTick();

    expect(spyPush).toHaveBeenCalledTimes(1);
    expect(spyPush).toHaveBeenCalledWith('/');
  });
});
