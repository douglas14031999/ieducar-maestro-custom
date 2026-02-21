import { VueWrapper, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest';
import HelloWorld from '@/components/HelloWorld.vue';

interface Props {
  msg: string;
}

const onMounted = async (props: Props) => {
  const wrapper = mount(HelloWorld, {
    props,
  });

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('HelloWorld', () => {
  beforeEach(async () => {
    const props: Props = {
      msg: 'Hello World',
    };

    wrapper = await onMounted(props);

    await wrapper.vm.$nextTick();
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  let wrapper: VueWrapper;

  test('the component exists', () => {
    expect(wrapper).toBeTruthy();
  });

  test('the component has a message passed by props', () => {
    expect(wrapper.find('h1').text()).toBe('Hello World');
  });
});
