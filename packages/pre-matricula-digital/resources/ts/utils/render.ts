import { Slots, VNode } from 'vue';

export function hSlot(slots: Slots) {
  const slot = slots.default;

  return slot !== undefined ? slot() : '';
}

export function hMergeSlot(slots: Slots, source: VNode[]) {
  const slot = slots.default;

  return slot !== undefined ? source.concat(slot()) : source;
}
