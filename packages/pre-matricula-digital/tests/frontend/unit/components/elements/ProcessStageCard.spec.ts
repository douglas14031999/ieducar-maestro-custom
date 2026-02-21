import { VueWrapper, mount } from '@vue/test-utils';
import ProcessStageCard from '@/components/elements/ProcessStageCard.vue';
import { Stages } from '@/types';
import filters from '@/filters';
import { expect, vi } from 'vitest';

vi.mock('vue-router/dist/vue-router.mjs', () => ({
  useRouter: () => ({
    push: spyPush,
  }),
}));

const spyPush = vi.fn();

const stageMock: Stages = {
  endAt: '2020-10-19 23:59:00',
  id: '1',
  name: 'Test',
  startAt: '2020-10-14 00:00:00',
  status: 'CLOSED',
  type: 'REGISTRATION_RENEWAL',
};

const onMounted = async (stage: Stages) => {
  const wrapper = mount(ProcessStageCard, {
    props: {
      stage,
    },
    global: {
      provide: {
        $filters: filters,
      },
    },
  });

  await wrapper.vm.$nextTick();

  return wrapper;
};

describe('ProcessStageCard', () => {
  let wrapper: VueWrapper<InstanceType<typeof ProcessStageCard>>;

  beforeEach(async () => {
    wrapper = await onMounted(stageMock);

    await wrapper.vm.$nextTick();
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  test('the component exists', () => {
    expect(wrapper).toBeTruthy();
  });

  test('the initial variables must be loaded with the correct data', () => {
    expect(wrapper.vm.stage).toMatchObject(stageMock);
  });

  test('card should be disabled', () => {
    const card = wrapper.find('.card-process-disabled');

    expect(card.exists()).toBeTruthy();
  });

  test('card should be enabled', async () => {
    wrapper = await onMounted({
      ...stageMock,
      status: 'OPEN',
    });

    const card = wrapper.find('.card-process-disabled');

    expect(card.exists()).toBeFalsy();
  });

  test('if status = OPEN, when I click on the button card, it should redirect to the process stage', async () => {
    wrapper = await onMounted({
      ...stageMock,
      status: 'OPEN',
    });

    const button = wrapper.find('[data-test="btn-process-stage-card"]');

    await button.trigger('click');

    expect(spyPush).toHaveBeenCalledWith({
      name: 'preregistration',
      params: {
        id: stageMock.id,
      },
    });
  });

  test("if status = CLOSED, when I click on the button card, it should'nt redirect to the process stage", async () => {
    const button = wrapper.find('[data-test="btn-process-stage-card"]');

    await button.trigger('click');

    expect(spyPush).toHaveBeenCalledTimes(0);
  });
});
