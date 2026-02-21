<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  modelValue?: boolean;
  alwaysShow?: boolean;
  external: unknown;
  internal: unknown;
  label: string;
  legend: string;
}>();

defineEmits(['update:modelValue']);

const canUpdate = computed(
  () => props.external !== props.internal && !!props.internal
);

const shouldDisplay = computed(() => {
  if (props.alwaysShow) {
    return true;
  }

  if (!props.internal) {
    return false;
  }

  return canUpdate.value;
});

const unique = Math.random().toString(36);
</script>

<template>
  <div v-if="shouldDisplay" class="d-flex border-top">
    <dl class="col-4 py-2 mb-0">
      <dt class="font-hind font-size-10">
        {{ label }}
      </dt>
      <dd class="mb-0" style="min-height: 24px">
        {{ external }}
      </dd>
    </dl>
    <dl class="col-4 py-2 mb-0 bg-white">
      <dt class="font-hind font-size-10">
        {{ label }}
      </dt>
      <dd class="mb-0" style="min-height: 24px">
        {{ internal }}
      </dd>
    </dl>
    <div class="col-4 py-2 mb-0 d-flex align-items-center">
      <div v-if="canUpdate" class="custom-control custom-checkbox">
        <input
          :id="unique"
          :value="modelValue"
          type="checkbox"
          class="custom-control-input"
          @input="$emit('update:modelValue', ($event.target as any).checked)"
        />
        <label :for="unique" class="custom-control-label font-size-10">
          {{ legend }}
        </label>
      </div>
    </div>
  </div>
</template>
