import { ProtocolStatusPreRegistration } from '@/modules/protocol/types';
import { Protocol as ProtocolApi } from '@/modules/protocol/api';
import { flushPromises, mount, VueWrapper } from '@vue/test-utils';
import { expect, vi } from 'vitest';
import VTooltip from 'v-tooltip';
import ProtocolConfirmKeepInWaitingList from '@/modules/protocol/components/ProtocolConfirmKeepInWaitingList.vue';

vi.mock('@/modules/protocol/api');

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
  parent: null,
  position: 1,
  process: {
    showPriorityProtocol: false,
    blockIncompatibleAgeGroup: false,
    forceSuggestedGrade: false,
    grades: [],
  },
  school: {
    area_code: 1,
    name: 'string',
    phone: 'string',
  },
  stage: {
    observation: 'string',
  },
  status: 'IN_CONFIRMATION',
  student: {
    dateOfBirth: 'string',
    initials: 'string',
  },
  type: 'string',
  waiting: null,
};

const onMounted = async (preregistration: {
  modelValue: boolean;
  preregistration: ProtocolStatusPreRegistration;
}) => {
  const wrapper = mount(ProtocolConfirmKeepInWaitingList, {
    props: preregistration,
    global: {
      stubs: ['modal'],
      directives: {
        tooltip: VTooltip,
      },
    },
  });

  await wrapper.vm.$nextTick();

  await flushPromises();

  return wrapper;
};

describe('ProtocolConfirmKeepInWaitingList', () => {
  let wrapper: VueWrapper<
    InstanceType<typeof ProtocolConfirmKeepInWaitingList>
  >;

  beforeEach(async () => {
    wrapper = await onMounted({
      modelValue: true,
      preregistration: preregistrationMock,
    });

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

  test('returnToWait must be executed successfully', async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockResolvedValueOnce({
      errors: false,
      keepOnTheWaitingList: {
        id: '1',
      },
    });

    const dialogSpy = vi.spyOn(wrapper.vm.ctx, 'dialog');

    await wrapper.vm.$nextTick();

    wrapper.vm.returnToWait();

    await flushPromises();

    expect(dialogSpy).toBeCalledTimes(1);
    expect(dialogSpy).toBeCalledWith({
      title: 'Sucesso!',
      description:
        'A permanência na lista de espera foi confirmada com sucesso.',
    });
  });

  test('returnToWait must be executed with errors', async () => {
    vi.mocked(ProtocolApi.postReturnToWait).mockRejectedValueOnce({
      response: {
        data: {
          errors: true,
        },
      },
    });

    const dialogSpy = vi.spyOn(wrapper.vm.ctx, 'dialog');

    await wrapper.vm.$nextTick();

    wrapper.vm.returnToWait();

    await flushPromises();

    expect(dialogSpy).toBeCalledTimes(1);
    expect(dialogSpy).toBeCalledWith({
      title: 'Erro!',
      description:
        'Não foi possível confirmar a permanência na lista de espera. ' +
        'Por favor, entre em contato com o suporte.',
      titleClass: 'danger',
    });
  });
});
