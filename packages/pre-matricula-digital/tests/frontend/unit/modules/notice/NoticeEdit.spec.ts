import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Notice } from '@/modules/notice/types';
import { Notice as NoticeApi } from '@/modules/notice/api';
import NoticeEdit from '@/modules/notice/components/NoticeEdit.vue';
import VTooltip from 'v-tooltip';
import { expect, vi } from 'vitest';
import { useModal } from '@/composables';

const dialogSpy = vi.fn();

vi.mock('@/composables/useModal');
vi.mock('@/modules/notice/api');

const notice: Notice = {
  id: 1,
  text: 'test notice',
};

const onMounted = async (response: Notice) => {
  vi.mocked(NoticeApi.list).mockReturnValueOnce(Promise.resolve(response));

  vi.mocked(useModal).mockImplementation(() => ({
    dialog: dialogSpy,
  }));

  const wrapper = mount(NoticeEdit, {
    global: {
      directives: {
        tooltip: VTooltip,
      },
      stubs: ['x-field', 'x-modal'],
    },
  });

  await flushPromises();

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('NoticeEdit', () => {
  let wrapper: VueWrapper<InstanceType<typeof NoticeEdit>>;

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
    const noticeDOM = wrapper.find('[type="RICH_TEXT"]');
    expect(noticeDOM.html()).toContain(notice.text);
  });

  test('notice value is empty', async () => {
    wrapper = await onMounted(undefined as unknown as Notice);

    expect(wrapper.vm.notice).toEqual({
      id: null,
      text: null,
    });
  });

  test('ao remover aviso, retorna erro', async () => {
    vi.mocked(NoticeApi.remove).mockRejectedValueOnce({});

    wrapper.vm.notice.text = '';

    const button = wrapper.find('[type="button"]');

    button.trigger('click');

    await flushPromises();
    await wrapper.vm.$nextTick();

    expect(dialogSpy).toHaveBeenCalledWith({
      title: 'Erro',
      description:
        'Não foi possível salvar o aviso. Por favor, entre em contato com o suporte.',
      titleClass: 'danger',
      iconLeft: 'status-red',
    });
  });

  test('remove aviso do sistema com sucesso', async () => {
    vi.mocked(NoticeApi.remove).mockReturnValueOnce(
      Promise.resolve({
        errors: false,
      })
    );

    wrapper.vm.notice.text = '';

    const button = wrapper.find('[type="button"]');

    button.trigger('click');

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(dialogSpy).toHaveBeenCalledWith({
      title: 'Sucesso!',
      titleClass: 'success',
      description: 'Aviso removido com sucesso!',
    });
    expect(wrapper.vm.notice.id).toEqual(notice.id);
  });

  test('adiciona um novo aviso', async () => {
    vi.mocked(NoticeApi.post).mockReturnValueOnce(
      Promise.resolve({
        errors: false,
      })
    );

    wrapper.vm.notice.text = 'Este é um novo aviso criado!';

    const button = wrapper.find('[type="button"]');

    button.trigger('click');

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(dialogSpy).toHaveBeenCalledWith({
      title: 'Sucesso!',
      titleClass: 'success',
      description: 'Aviso salvo com sucesso!',
    });
    expect(wrapper.vm.notice).toEqual({
      id: notice.id,
      text: 'Este é um novo aviso criado!',
    });
  });

  test('ao adicionar um novo aviso, retorna erro', async () => {
    vi.mocked(NoticeApi.post).mockRejectedValueOnce({});

    wrapper.vm.notice.text = 'Aqui vai um teste com erro desconhecido';

    const button = wrapper.find('[type="button"]');

    button.trigger('click');

    await wrapper.vm.$nextTick();

    await flushPromises();

    expect(dialogSpy).toHaveBeenCalledWith({
      title: 'Erro',
      description:
        'Não foi possível salvar o aviso. Por favor, entre em contato com o suporte.',
      titleClass: 'danger',
      iconLeft: 'status-red',
    });
  });
});
