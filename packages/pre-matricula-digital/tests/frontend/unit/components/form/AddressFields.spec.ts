import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Address } from '@/types';
import AddressFields from '@/components/form/AddressFields.vue';
import { createTestingPinia } from '@pinia/testing';
import { initialize } from '../../mocks/google';
import { expect, vi } from 'vitest';

interface AddressFieldsProps {
  data: Address;
  name: string;
  setFieldValue: (name: string, value: number | string) => void;
  errors: { [key: string]: boolean };
  fetchingAddressLatLng: boolean;
}

const propsMock: AddressFieldsProps = {
  data: {
    postalCode: '00000-000',
    address: 'string',
    number: 'string',
    complement: 'string',
    neighborhood: 'string',
    city: 'Some City',
    stateAbbreviation: 'AA',
    cityIbgeCode: 12345,
    lat: 0,
    lng: 0,
  },
  name: '',
  setFieldValue: vi.fn(),
  errors: {},
  fetchingAddressLatLng: false,
};

const onMounted = async (props: AddressFieldsProps) => {
  const wrapper = mount(AddressFields, {
    props,
    global: {
      plugins: [createTestingPinia()],
      stubs: ['x-field'],
    },
    attachTo: document.body,
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('AddressFields', () => {
  let wrapper: VueWrapper<InstanceType<typeof AddressFields>>;

  beforeEach(async () => {
    initialize();

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

  test('should call updateAddress function', async () => {
    const spy = vi.spyOn(wrapper.vm.ctx, 'isCityPostalCode');

    spy.mockImplementationOnce(() => true);

    wrapper.vm.cityPostalCode = undefined;

    const param = {
      logradouro: 'Some Street',
      complemento: 'Some Complement',
      bairro: 'Some Neighborhood',
      localidade: 'Some City 2',
      uf: 'BB',
      ibge: '54321',
    };

    wrapper.vm.updateAddress(param);

    await wrapper.vm.$nextTick();

    expect(spy).toBeCalled();
    expect(spy).toBeCalledTimes(1);

    expect(propsMock.setFieldValue).toBeCalled();
    expect(propsMock.setFieldValue).toBeCalledTimes(3);

    expect(wrapper.vm.modelData.address).toBe(param.logradouro);
    expect(wrapper.vm.modelData.complement).toBe(param.complemento);
    expect(wrapper.vm.modelData.neighborhood).toBe(param.bairro);
    expect(wrapper.vm.modelData.city).toBe(param.localidade);
    expect(wrapper.vm.modelData.stateAbbreviation).toBe(param.uf);
    expect(wrapper.vm.modelData.cityIbgeCode).toBe(Number(param.ibge));
  });

  test('must have 5 x-fields', () => {
    const fields = wrapper.findAll('x-field-stub');

    expect(fields).toHaveLength(5);
  });

  describe('checkError', () => {
    test('checkError function must return correct true value', async () => {
      wrapper = await onMounted({
        ...propsMock,
        errors: {
          'address.postalCode': true,
        },
      });

      await wrapper.vm.$nextTick();

      const response = wrapper.vm.checkError('address');

      await wrapper.vm.$nextTick();

      expect(response).toBe(true);
    });

    test('checkError function must return correct false value', async () => {
      wrapper = await onMounted({
        ...propsMock,
        errors: {
          'address.postalCode': false,
        },
      });

      await wrapper.vm.$nextTick();

      const response = wrapper.vm.checkError('address');

      await wrapper.vm.$nextTick();

      expect(response).toBe(false);
    });

    test('checkError function must return correct false value when key is not found', async () => {
      wrapper = await onMounted({
        ...propsMock,
        errors: {},
      });

      await wrapper.vm.$nextTick();

      const response = wrapper.vm.checkError('address');

      await wrapper.vm.$nextTick();

      expect(response).toBe(false);
    });
  });
});
