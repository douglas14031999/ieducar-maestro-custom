<template>
  <draggable v-model="model" class="row" draggable=".can-drag" item-key="id">
    <template #item="{ element }">
      <div
        :class="{
          'col-12 col-md-6': element.type !== 'ADDRESS',
          'col-12': element.type === 'ADDRESS',
        }"
        class="can-drag mb-3"
      >
        <label
          v-if="element.mandatory"
          class="toggle-checkbox"
          @click="changeField(element)"
        >
          <input
            type="checkbox"
            :checked="element.required"
            :value="element.id"
            disabled
          />
          <div
            :class="{
              'toggle-required': element.required,
              'toggle-weight': element.priority,
            }"
            class="toggle-text text-break overflow-hidden"
          >
            {{ element.name }}
          </div>
        </label>
        <label v-else class="toggle-checkbox">
          <input
            v-model="element.checked"
            type="checkbox"
            :checked="element.required"
            :value="element.id"
            @change="changeField(element)"
          />
          <div
            :class="{
              'toggle-required': element.required,
              'toggle-weight': element.priority,
            }"
            class="toggle-text"
          >
            {{ element.name }}
          </div>
        </label>
        <modal
          v-model="element.open"
          :title="element.name"
          title-class="text-uppercase text-primary font-weight-bold"
          no-footer
        >
          <template #body>
            <div class="custom-control custom-checkbox">
              <input
                :id="`required-responsible-${element.id}`"
                v-model="element.required"
                :disabled="element.mandatory"
                type="checkbox"
                class="custom-control-input"
              />
              <label
                :for="`required-responsible-${element.id}`"
                class="custom-control-label"
              >
                Obrigatoriedade de resposta
              </label>
            </div>
            <hr />
            <div class="custom-control custom-checkbox">
              <input
                :id="`priority-responsible-${element.id}`"
                v-model="element.priority"
                type="checkbox"
                class="custom-control-input"
              />
              <label
                :for="`priority-responsible-${element.id}`"
                class="custom-control-label"
              >
                Usar como critério de priorização
              </label>
            </div>
            <template v-if="element.priority">
              <x-field
                v-if="element.type === 'DATE'"
                v-model="element.weight"
                type="RADIO"
                name="weight"
                label="Prioridade de Vaga"
                container-class="mt-3"
                :options="[
                  {
                    key: -1,
                    label: `Prioriza <b>${element.name}</b> recente`,
                  },
                  {
                    key: 1,
                    label: `Prioriza <b>${element.name}</b> antiga`,
                  },
                ]"
              />
              <div v-else class="mt-4">
                <label class="form-label" for="priority-level">
                  Nível de prioridade do campo:
                </label>
                <div class="mb-3">
                  <input
                    id="priority-level"
                    v-model="element.weight"
                    class="form-control"
                    type="number"
                    min="0"
                    max="100"
                  />
                </div>
                <input
                  v-model="element.weight"
                  type="range"
                  class="custom-range"
                  max="100"
                />
                <div class="d-flex justify-content-between">
                  <small class="text-muted">Menos prioridade</small>
                  <small class="text-muted">Mais prioridade</small>
                </div>
              </div>
            </template>
            <x-btn
              label="Salvar"
              color="primary"
              class="w-100 mt-4"
              no-caps
              no-wrap
              @click="element.open = false"
            />
          </template>
        </modal>
      </div>
    </template>
  </draggable>
</template>

<script setup lang="ts">
import { Field } from '../types';
import Modal from '@/components/elements/Modal.vue';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XField from '@/components/x-form/XField.vue';
import draggable from 'vuedraggable';
import { useVModel } from '@vueuse/core';

const props = defineProps<{
  modelValue: Field[];
}>();

const model = useVModel(props, 'modelValue');

const changeField = (field: Field) => {
  const internalField = field as unknown as {
    id: string;
    required: boolean;
    priority: boolean;
    weight: number;
    checked: boolean;
    open: boolean;
  };

  if (internalField.checked) {
    internalField.open = true;
  } else {
    internalField.priority = false;
    internalField.open = false;
    internalField.required = false;
    internalField.weight = 0;
  }
};
</script>
