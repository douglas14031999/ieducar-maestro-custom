import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Field as FieldApi } from '@/modules/fields/api';
import { FieldPage } from '@/modules/fields/types';
import FieldsPage from '@/modules/fields/components/FieldsPage.vue';
import VTooltip from 'v-tooltip';
import { vi, expect } from 'vitest';
import { useModal } from '@/composables';

const dialogSpy = vi.fn();

vi.mock('@/composables/useModal');
vi.mock('@/modules/fields/api');

const fields: FieldPage[] = [
  {
    group: 'RESPONSIBLE',
    id: '2',
    internal: true,
    name: 'Data de nascimento',
    options: [],
    required: true,
    type: 'DATE',
  },
  {
    group: 'RESPONSIBLE',
    id: '3',
    internal: true,
    name: 'CPF',
    options: [],
    required: true,
    type: 'CPF',
  },
  {
    group: 'RESPONSIBLE',
    id: '7',
    internal: true,
    name: 'Naturalidade',
    options: [],
    required: false,
    type: 'CITY',
  },
  {
    group: 'STUDENT',
    id: '9',
    internal: true,
    name: 'Nome',
    options: [],
    required: true,
    type: 'TEXT',
  },
  {
    group: 'STUDENT',
    id: '10',
    internal: true,
    name: 'Data de nascimento',
    options: [],
    required: true,
    type: 'DATE',
  },
  {
    group: 'STUDENT',
    id: '13',
    internal: true,
    name: 'Certidão de nascimento',
    options: [],
    required: false,
    type: 'BIRTH_CERTIFICATE',
  },
];

const responsible = fields.filter((field) => field.group === 'RESPONSIBLE');

const student = fields.filter((field) => field.group === 'STUDENT');

const onMounted = async (payload: FieldPage[], errors?: boolean) => {
  if (!errors) {
    vi.mocked(FieldApi.list).mockResolvedValueOnce(payload);
  } else {
    vi.mocked(FieldApi.list).mockRejectedValueOnce({});
  }

  vi.mocked(useModal).mockImplementation(() => ({
    dialog: dialogSpy,
  }));

  const wrapper = mount(FieldsPage, {
    global: {
      directives: {
        tooltip: VTooltip,
      },
      stubs: ['x-field'],
    },
  });

  await flushPromises();

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('FieldsPage', () => {
  let wrapper: VueWrapper<InstanceType<typeof FieldsPage>>;

  beforeEach(async () => {
    wrapper = await onMounted(fields);

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

  test('trás os dados iniciais do servidor', () => {
    expect(wrapper.vm.fields).toMatchObject(fields);
    expect(wrapper.vm.responsible).toMatchObject(responsible);
    expect(wrapper.vm.student).toMatchObject(student);
  });

  test('deve listar os conteúdos dos campos de responsible e student', async () => {
    const contentList = wrapper.findAll('label.toggle-checkbox');

    // o valor de fields.length deve somar 1 pelo fato de que o campo de responsável
    // sempre terá um valor de endereço escrito de maneira hard coded, caso o responsible > 0

    expect(contentList.length).toEqual(fields.length + 1);
  });

  test('ao clicar no primeiro checkbox, deverá aparecer o modal para editar o campo', async () => {
    expect(wrapper.vm.open).toBeFalsy();

    const spy = vi.spyOn(wrapper.vm, 'openModalToUpdate');

    const wrapperCheckbox = wrapper.findAll(
      '[data-test="wrapper-checkbox"]'
    )[0];

    await wrapperCheckbox.trigger('click');

    expect(spy).toBeCalled();
    expect(wrapper.vm.open).toBeTruthy();
    expect(wrapper.vm.field).toEqual({
      ...wrapper.vm.newField,
      ...responsible[0],
    });
    expect(wrapper.find('.x-modal')).toBeTruthy();

    vi.restoreAllMocks();
  });

  test('não deve mostrar os campos se fields.length === 0', async () => {
    wrapper = await onMounted([], true);

    const contentList = wrapper.findAll('label.toggle-checkbox');

    expect(contentList.length).toEqual(0);

    wrapper.unmount();
    vi.restoreAllMocks();
  });

  describe('addField', () => {
    test('when I click on the button to add a new student field, addField function must be called', async () => {
      wrapper.vm.addField = vi.fn();

      const btn = wrapper.find('[data-test="btn-add-field-student"]');

      await btn.trigger('click');

      expect(wrapper.vm.addField).toBeCalledTimes(1);
      expect(wrapper.vm.addField).toBeCalledWith('STUDENT');
    });

    test('when I click on the button to add a new responsible field, addField function must be called', async () => {
      wrapper.vm.addField = vi.fn();

      const btn = wrapper.find('[data-test="btn-add-field-responsible"]');

      await btn.trigger('click');

      expect(wrapper.vm.addField).toBeCalledTimes(1);
      expect(wrapper.vm.addField).toBeCalledWith('RESPONSIBLE');
    });

    test('when addField is called with RESPONSIBLE param, modal variable must be true, and field variable must be filled', async () => {
      wrapper.vm.addField('RESPONSIBLE');

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.open).toBeTruthy();
      expect(wrapper.vm.field).toMatchObject({
        id: null,
        name: null,
        internal: false,
        deleteOptions: [],
        group: 'RESPONSIBLE',
        options: [],
        type: 'TEXT',
      });
    });

    test('when addField is called with STUDENT param, modal variable must be true, and field variable must be filled', async () => {
      wrapper.vm.addField('STUDENT');

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.open).toBeTruthy();
      expect(wrapper.vm.field).toMatchObject({
        id: null,
        name: null,
        internal: false,
        deleteOptions: [],
        group: 'STUDENT',
        options: [],
        type: 'TEXT',
      });
    });
  });

  describe('openModalToUpdate', () => {
    test('when openModalToUpdate is called, modal variable must be true', async () => {
      wrapper.vm.openModalToUpdate(responsible[0]);

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.open).toBeTruthy();
      expect(wrapper.vm.field).toMatchObject({
        id: '2',
        internal: true,
        deleteOptions: [],
        group: 'RESPONSIBLE',
        options: [],
        name: 'Data de nascimento',
        required: true,
        type: 'DATE',
      });
    });
  });

  describe('createField', () => {
    test('when createField is called, it must call createField function', async () => {
      wrapper.vm.createField = vi.fn();

      wrapper.vm.createField();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.createField).toBeCalledTimes(1);
    });

    test('must finish the requisition with successfull', async () => {
      vi.mocked(FieldApi.post).mockResolvedValueOnce({});
      vi.mocked(FieldApi.list).mockResolvedValueOnce(fields);

      wrapper.vm.open = true;

      wrapper.vm.createField();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(wrapper.vm.open).toBeFalsy();
    });

    test('must finish the requisition with error', async () => {
      vi.mocked(FieldApi.post).mockRejectedValueOnce({});

      wrapper.vm.open = true;

      wrapper.vm.createField();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(wrapper.vm.open).toBeTruthy();
      expect(dialogSpy).toHaveBeenCalledWith({
        title: 'Erro ao criar o campo',
        description:
          'Aconteceu um erro ao criar o campo, por favor tente novamente em alguns instantes.',
        titleClass: 'danger',
        iconLeft: 'status-red',
      });
    });
  });

  describe('updateField', () => {
    test('when updateField is called, it must call updateField function', async () => {
      wrapper.vm.updateField = vi.fn();

      wrapper.vm.updateField();

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.updateField).toBeCalledTimes(1);
    });

    test('must finish the requisition with successfull', async () => {
      vi.mocked(FieldApi.put).mockResolvedValueOnce({});
      vi.mocked(FieldApi.list).mockResolvedValueOnce(fields);

      wrapper.vm.open = true;

      wrapper.vm.updateField();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(wrapper.vm.open).toBeFalsy();
    });

    test('must finish the requisition with error', async () => {
      vi.mocked(FieldApi.put).mockRejectedValueOnce({});

      wrapper.vm.open = true;

      wrapper.vm.updateField();

      await wrapper.vm.$nextTick();

      await flushPromises();

      expect(wrapper.vm.open).toBeTruthy();
      expect(dialogSpy).toHaveBeenCalledWith({
        title: 'Erro ao editar o campo',
        description:
          'Aconteceu um erro ao editar o campo, por favor tente novamente em alguns instantes.',
        titleClass: 'danger',
        iconLeft: 'status-red',
      });
    });
  });

  describe('saveField', () => {
    beforeEach(async () => {
      wrapper = await onMounted(fields);

      await wrapper.vm.$nextTick();
    });

    afterEach(() => {
      vi.clearAllMocks();
    });

    test('should exec createField function when field.id is null', async () => {
      vi.mocked(FieldApi.post).mockResolvedValueOnce({});
      vi.mocked(FieldApi.list).mockResolvedValueOnce(fields);

      wrapper.vm.saveField({
        ...responsible[0],
        id: null,
      });

      await wrapper.vm.$nextTick();

      await flushPromises();

      // if this expect assert to be true, it means that the createField function was called
      expect(wrapper.vm.open).toBeFalsy();
    });

    test('should exec updateField function when field.id is filled', async () => {
      vi.mocked(FieldApi.put).mockResolvedValueOnce({});
      vi.mocked(FieldApi.list).mockResolvedValueOnce(fields);

      wrapper.vm.saveField(responsible[0]);

      await wrapper.vm.$nextTick();

      await flushPromises();

      // if this expect assert to be true, it means that the updateField function was called
      expect(wrapper.vm.open).toBeFalsy();
    });
  });
});
