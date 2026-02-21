import {
  ParseFieldFromProcess,
  PreRegistrationResponsibleField,
} from '@/modules/preregistration/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import ResponsibleFields from '@/modules/preregistration/components/ResponsibleFields.vue';
import VTooltip from 'v-tooltip';
import { createTestingPinia } from '@pinia/testing';
import filters from '@/filters';
import { newAddress } from '@/util';
import { useGeneralStore } from '@/store/general';
import { expect, vi } from 'vitest';

interface ResponsibleFieldsProps {
  responsible: PreRegistrationResponsibleField;
  errors: {
    [key: string]: boolean;
  };
  fields: ParseFieldFromProcess[];
  fetchingPrimaryAddressLatLng: boolean;
  fetchingSecondaryAddressLatLng: boolean;
  setFieldValue: (field: string, value: string | number) => void;
}

const propsMock: ResponsibleFieldsProps = {
  responsible: {
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
  errors: {},
  fields: [],
  fetchingPrimaryAddressLatLng: false,
  fetchingSecondaryAddressLatLng: false,
  setFieldValue: () => {
    return;
  },
};

const onMounted = async (props: ResponsibleFieldsProps) => {
  const wrapper = mount(ResponsibleFields, {
    props,
    global: {
      plugins: [createTestingPinia()],
      stubs: ['x-field', 'address-fields'],
      directives: {
        tooltip: VTooltip,
      },
      provide: {
        $filters: filters,
      },
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  const store = useGeneralStore();

  store.config.allow_optional_address = true;

  return wrapper;
};

describe('ResponsibleFields', () => {
  let wrapper: VueWrapper<InstanceType<typeof ResponsibleFields>>;

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

  describe('useSecondAddress', () => {
    beforeEach(async () => {
      wrapper = await onMounted(propsMock);

      await wrapper.vm.$nextTick();
    });

    afterEach(() => {
      vi.clearAllMocks();
    });

    test('when I click in use second address button, modelResponsible.useSecondAddress must be set to true', async () => {
      wrapper.vm.modelResponsible.useSecondAddress = false;

      expect(wrapper.vm.modelResponsible.useSecondAddress).toBeFalsy();

      const btn = wrapper.find('[data-test="btn-use-second-address"]');

      await btn.trigger('click');

      expect(wrapper.vm.modelResponsible.useSecondAddress).toBeTruthy();
    });

    test("when I click in doesn't use second address button, the doesntuseSecondAddress function must be called", async () => {
      wrapper.vm.modelResponsible.useSecondAddress = true;

      expect(wrapper.vm.modelResponsible.useSecondAddress).toBeTruthy();

      await wrapper.vm.$nextTick();

      const btn = wrapper.find('[data-test="btn-doenst-use-second-address"]');

      await btn.trigger('click');

      await wrapper.vm.$nextTick();

      expect(wrapper.vm.responsible.useSecondAddress).toBe(false);
      expect(wrapper.vm.responsible.secondAddress).toMatchObject(newAddress());
    });
  });
});
