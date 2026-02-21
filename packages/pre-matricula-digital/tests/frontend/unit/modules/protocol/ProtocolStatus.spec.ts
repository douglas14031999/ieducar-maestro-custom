import {
  ProtocolStatusPreRegistration,
  ProtocolStatusReturnToWait,
} from '@/modules/protocol/types';
import { VueWrapper, flushPromises, mount } from '@vue/test-utils';
import { Protocol as ProtocolApi } from '@/modules/protocol/api';
import ProtocolStatus from '@/modules/protocol/components/ProtocolStatus.vue';
import { createTestingPinia } from '@pinia/testing';
import { useGeneralStore } from '@/store/general';
import VTooltip from 'v-tooltip';
import filters from '@/filters';
import { expect, vi } from 'vitest';
import { Nullable } from '@/types';

vi.mock('@/modules/protocol/api');
vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRoute: () => ({
    params: {
      id: '000AAA',
    },
  }),
}));
vi.mock('vee-validate');

const preregistrationMock: ProtocolStatusPreRegistration = {
  classroom: {
    grade: {
      name: 'string',
    },
    name: 'string',
    period: {
      name: 'string',
    },
  },
  id: 'string',
  observation: 'string',
  parent: {
    id: 1,
    protocol: 'string',
    code: 'string',
    type: 'string',
    date: 'string',
    position: 1,
    school: {
      id: 1,
      name: 'string',
      area_code: 'string',
      phone: 'string',
    },
    process: {
      showPriorityProtocol: false,
    },
  },
  position: 1,
  process: {
    showPriorityProtocol: false,
    blockIncompatibleAgeGroup: false,
    forceSuggestedGrade: false,
    grades: [],
  },
  school: {
    area_code: 47,
    name: 'string',
    phone: 'string',
  },
  stage: {
    observation: 'string',
  },
  status: 'ACCEPTED',
  student: {
    dateOfBirth: 'string',
    initials: 'string',
  },
  type: 'string',
  waiting: {
    id: 1,
    protocol: 'string',
    code: 'string',
    type: 'string',
    date: 'string',
    position: 1,
    school: {
      id: 1,
      name: 'string',
      area_code: 'string',
      phone: 'string',
    },
    process: {
      showPriorityProtocol: false,
    },
  },
};

const returnToWaitResponseMock: ProtocolStatusReturnToWait = {
  errors: false,
  keepOnTheWaitingList: {
    id: '1',
  },
};

const onMounted = async (
  preregistration: Nullable<ProtocolStatusPreRegistration>
) => {
  vi.mocked(ProtocolApi.show).mockResolvedValueOnce(preregistration);

  const wrapper = mount(ProtocolStatus, {
    plugins: [createTestingPinia()],
    global: {
      stubs: [
        'protocol-consult',
        'router-link',
        'protocol-confirm-keep-in-waiting-list',
      ],
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

  return wrapper;
};

describe('ProtocolStatus', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProtocolStatus>>;

  beforeEach(async () => {
    wrapper = await onMounted(preregistrationMock);

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

  test('check if I have 1 protocol-consult, pre-registration-position and 3 modals', () => {
    const protocolConsult = wrapper.findComponent({
      name: 'protocol-consult',
    });

    expect(protocolConsult).toBeTruthy();

    const preRegistrationPosition = wrapper.findComponent({
      name: 'pre-registration-position',
    });

    expect(preRegistrationPosition).toBeTruthy();

    const modals = wrapper.findAllComponents({
      name: 'modal',
    });

    expect(modals).toBeTruthy();
    expect(modals.length).toBe(1);
  });

  test('the component has the correct data', () => {
    expect(wrapper.vm.preregistration).toMatchObject(preregistrationMock);
    expect(wrapper.vm.notFound).toBeFalsy();
  });

  test('when I click in the "Mais informações" button, the modal is open', async () => {
    const button = wrapper.find('[data-test="btn-more-info"]');

    await button.trigger('click');

    expect(wrapper.vm.showModal).toBeTruthy();
  });

  test('"Continuar na lista" button, should not be shown if isAthenticated is false', async () => {
    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'IN_CONFIRMATION';

    wrapper = await onMounted(preregistrationMockNewStatus);

    const store = useGeneralStore();

    store.auth.authenticated = false;

    await wrapper.vm.$nextTick();

    const button = wrapper.find('[data-test="btn-continue-on-the-list"]');

    expect(button.exists()).toBeFalsy();
  });

  test('when I click in the "Continuar na lista" button, with status IN_CONFIRMATION the modal is open and must to trigger returnToWait function', async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'IN_CONFIRMATION';

    wrapper = await onMounted(preregistrationMockNewStatus);

    const store = useGeneralStore();

    store.auth.authenticated = true;

    await wrapper.vm.$nextTick();

    const button = wrapper.find('[data-test="btn-continue-on-the-list"]');

    await button.trigger('click');

    expect(wrapper.vm.showModalConfirmKeepInWaitingList).toBeTruthy();
  });

  test('when I click in the "Continuar na lista" button, with status IN_CONFIRMATION the modal is open and must to trigger returnToWait function and return error', async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockRejectedValueOnce({
      response: {
        data: {
          ...returnToWaitResponseMock,
          errors: true,
        },
      },
    });

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'IN_CONFIRMATION';

    wrapper = await onMounted(preregistrationMockNewStatus);

    await wrapper.vm.$nextTick();

    const store = useGeneralStore();

    store.auth.authenticated = true;

    await wrapper.vm.$nextTick();

    const button = wrapper.find('[data-test="btn-continue-on-the-list"]');

    await button.trigger('click');

    expect(wrapper.vm.showModalConfirmKeepInWaitingList).toBeTruthy();
  });

  test("If the status of preregistration is WAITING, the alert label must be 'Em espera'", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'WAITING';

    wrapper = await onMounted(preregistrationMockNewStatus);

    const label = wrapper.find(
      '[class="alert badge-yellow text-center text-uppercase"]'
    );

    expect(label.text()).toBe('Em espera');
  });

  test("If the status of preregistration is REJECTED, the alert label must be 'Indeferido'", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'REJECTED';

    wrapper = await onMounted(preregistrationMockNewStatus);

    const label = wrapper.find(
      '[class="alert badge-red text-center text-uppercase"]'
    );

    expect(label.text()).toBe('Indeferido');
  });

  test("If the status of preregistration is SUMMONED, the alert label must be 'Responsáveis Convocados'", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'SUMMONED';

    wrapper = await onMounted(preregistrationMockNewStatus);

    const label = wrapper.find(
      '[class="alert badge-purple text-center text-uppercase"]'
    );

    expect(label.text()).toBe('Responsáveis Convocados');
  });

  test("If showPriorityProtocol is true and status is WAITING, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'WAITING';

    preregistrationMockNewStatus.process.showPriorityProtocol = true;

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position"]'
    );

    expect(preRegistrationPosition.exists()).toBeTruthy();
  });

  test("If showPriorityProtocol is true and status is ACCEPTED, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'ACCEPTED';

    preregistrationMockNewStatus.process.showPriorityProtocol = true;

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position"]'
    );

    expect(preRegistrationPosition.exists()).toBeTruthy();
  });

  test("If showPriorityProtocol is true and status is REJECTED, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'REJECTED';

    preregistrationMockNewStatus.process.showPriorityProtocol = true;

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position"]'
    );

    expect(preRegistrationPosition.exists()).toBeTruthy();
  });

  test("If showPriorityProtocol is true and status is SUMMONED, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'SUMMONED';

    preregistrationMockNewStatus.process.showPriorityProtocol = true;

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position"]'
    );

    expect(preRegistrationPosition.exists()).toBeTruthy();
  });

  test("If showPriorityProtocol is false and status is WAITING, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'WAITING';

    preregistrationMockNewStatus.process.showPriorityProtocol = false;

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position"]'
    );

    expect(preRegistrationPosition.exists()).toBeFalsy();
  });

  test("If showPriorityProtocol is false and status is ACCEPTED, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'ACCEPTED';

    preregistrationMockNewStatus.process.showPriorityProtocol = false;

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position"]'
    );

    expect(preRegistrationPosition.exists()).toBeFalsy();
  });

  test("If showPriorityProtocol is false and status is REJECTED, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'REJECTED';

    preregistrationMockNewStatus.process.showPriorityProtocol = false;

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position"]'
    );

    expect(preRegistrationPosition.exists()).toBeFalsy();
  });

  test("If showPriorityProtocol is false and status is SUMMONED, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'SUMMONED';

    preregistrationMockNewStatus.process.showPriorityProtocol = false;

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position"]'
    );

    expect(preRegistrationPosition.exists()).toBeFalsy();
  });

  test("If process is waiting another process, and this waiting process is showing priority protocol, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'WAITING';

    preregistrationMockNewStatus.process.showPriorityProtocol = false;

    if (preregistrationMockNewStatus.waiting.process) {
      preregistrationMockNewStatus.waiting.process.showPriorityProtocol = true;
    }

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position-waiting"]'
    );

    expect(preRegistrationPosition.exists()).toBeTruthy();
  });

  test("If process is waiting another process, and this waiting process isn't showing priority protocol, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'WAITING';

    preregistrationMockNewStatus.process.showPriorityProtocol = false;

    if (preregistrationMockNewStatus.waiting.process) {
      preregistrationMockNewStatus.waiting.process.showPriorityProtocol = false;
    }

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position-waiting"]'
    );

    expect(preRegistrationPosition.exists()).toBeFalsy();
  });

  test("If process have parent, and this parent process is showing priority protocol, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'WAITING';

    preregistrationMockNewStatus.process.showPriorityProtocol = false;

    if (preregistrationMockNewStatus.parent.process) {
      preregistrationMockNewStatus.parent.process.showPriorityProtocol = true;
    }

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position-parent"]'
    );

    expect(preRegistrationPosition.exists()).toBeTruthy();
  });

  test("If process have parent, and this parent process isn't showing priority protocol, the 'pre-registration-position' component must be mounted", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'WAITING';

    preregistrationMockNewStatus.process.showPriorityProtocol = false;

    if (preregistrationMockNewStatus.parent.process) {
      preregistrationMockNewStatus.parent.process.showPriorityProtocol = false;
    }

    wrapper = await onMounted(preregistrationMockNewStatus);

    const preRegistrationPosition = wrapper.find(
      '[data-test="preregistration-position-parent"]'
    );

    expect(preRegistrationPosition.exists()).toBeFalsy();
  });

  test("If process status is 'IN_CONFIRMATION' and I click in 'Mais informações' button, the showModal variable must be true", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      returnToWaitResponseMock
    );

    const preregistrationMockNewStatus = preregistrationMock;

    preregistrationMockNewStatus.status = 'IN_CONFIRMATION';

    wrapper = await onMounted(preregistrationMockNewStatus);

    expect(wrapper.vm.showModal).toBeFalsy();

    const btnMoreInfo = wrapper.find('[data-test="btn-more-info"]');

    await btnMoreInfo.trigger('click');

    expect(wrapper.vm.showModal).toBeTruthy();
  });

  test("If 'notFound' variable is true and preregistration is null, properly text must be rendered", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      null as unknown as ProtocolStatusReturnToWait
    );

    wrapper = await onMounted(null);

    expect(wrapper.vm.preregistration).toBeNull();

    expect(wrapper.vm.notFound).toBeTruthy();

    const notFoundText = wrapper.find('[data-test="not-found-text"]');

    expect(notFoundText.exists()).toBeTruthy();

    expect(notFoundText.text()).toBe('Nenhum protocolo foi encontrado.');
  });

  test("If 'notFound' variable is false and preregistration is null, loader component must be rendered", async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce(
      null as unknown as ProtocolStatusReturnToWait
    );

    wrapper = await onMounted(null);

    wrapper.vm.notFound = false;

    await wrapper.vm.$nextTick();

    expect(wrapper.vm.preregistration).toBeNull();

    expect(wrapper.vm.notFound).toBeFalsy();

    const loader = wrapper.find('[data-test="loader-content"]');

    expect(loader.exists()).toBeTruthy();
  });
});
