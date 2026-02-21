import Fields, { GenericFields } from '@/components/form/Fields.vue';
import { Option, Rules } from '@/types';
import {
  ParseFieldFromProcess,
  PreRegistrationResponsibleField,
} from '@/modules/preregistration/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { expect, vi } from 'vitest';

interface FieldsProps {
  data: PreRegistrationResponsibleField;
  fields: ParseFieldFromProcess[];
  errors: Rules;
  cities: Option[];
}

const propsMock: FieldsProps = {
  errors: {},
  cities: [],
  fields: [
    {
      id: 'string',
      key: 'string',
      rules: {},
      classes: {},
      type: 'TEXT',
      label: 'string',
      order: '0',
      field: {
        id: 'string',
        name: 'string',
        group: 'string',
        internal: 'responsible_name',
        type: 'TEXT',
        options: [],
      },
      required: true,
      weight: 0,
      filter: (value: string) => value,
    },
  ],
  data: {
    relationType: null,
    address: {
      postalCode: '',
      address: '',
      number: '',
      complement: '',
      neighborhood: '',
      lat: 0,
      lng: 0,
      city: '',
      cityIbgeCode: 0,
      stateAbbreviation: '',
    },
    useSecondAddress: false,
    secondAddress: {
      postalCode: '',
      address: '',
      number: '',
      complement: '',
      neighborhood: '',
      lat: 0,
      lng: 0,
      city: '',
      cityIbgeCode: 0,
      stateAbbreviation: '',
    },
    responsible_name: null,
  } as PreRegistrationResponsibleField,
};

const onMounted = async (props: FieldsProps) => {
  const wrapper = mount(GenericFields<PreRegistrationResponsibleField>(), {
    props,
    global: {
      stubs: ['x-field'],
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('Fields', () => {
  let wrapper: VueWrapper<InstanceType<typeof Fields>>;

  beforeEach(async () => {
    wrapper = await onMounted(propsMock);

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

  test('one field must be rendered', () => {
    const fields = wrapper.findAll('x-field-stub');

    expect(fields.length).toBe(1);
  });

  test('zero fields must be rendered', async () => {
    wrapper = await onMounted({
      ...propsMock,
      fields: [],
    });

    const fields = wrapper.findAll('x-field-stub');

    expect(fields.length).toBe(0);
  });
});
