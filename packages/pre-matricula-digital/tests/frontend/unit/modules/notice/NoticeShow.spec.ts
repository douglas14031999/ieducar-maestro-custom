import { VueWrapper, flushPromises, shallowMount } from '@vue/test-utils';
import { Notice } from '@/modules/notice/types';
import { Notice as NoticeApi } from '@/modules/notice/api';
import NoticeShow from '@/modules/notice/components/NoticeShow.vue';
import { expect, vi } from 'vitest';

vi.mock('@/modules/notice/api');

const notice: Notice = {
  id: 1,
  text: 'test notice',
};

const onMounted = async (response: Notice) => {
  vi.mocked(NoticeApi.list).mockReturnValueOnce(Promise.resolve(response));

  const wrapper = shallowMount(NoticeShow);

  await flushPromises();

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('NoticeShow', () => {
  let wrapper: VueWrapper<InstanceType<typeof NoticeShow>>;

  beforeEach(async () => {
    wrapper = await onMounted(notice);

    await wrapper.vm.$nextTick();
  });

  afterEach(() => {
    vi.resetAllMocks();
  });

  test('the component exists', () => {
    expect(wrapper).toBeTruthy();
  });

  test('should match snapshot', () => {
    expect(wrapper.element).toMatchSnapshot();
  });

  test('notice value is successfully loaded', () => {
    expect(wrapper.vm.notice).toEqual(notice);
  });

  test('the value of notice is printed in the DOM', () => {
    const noticeDOM = wrapper.find('[data-test="notice-section"]');
    expect(noticeDOM.html()).toContain(notice.text);
  });

  test('notice value is empty', async () => {
    wrapper = await onMounted(undefined as unknown as Notice);

    expect(wrapper.vm.notice).toEqual({
      id: null,
      text: null,
    });
  });
});
