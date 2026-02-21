<template>
  <FormComponent v-slot="slot" ref="form" :initial-values="iniValues" as="div">
    <form
      class="row"
      autocomplete="off"
      @submit.prevent.stop="handleSubmit(slot)"
    >
      <template v-for="field in schema.fields" :key="field.name">
        <x-field
          v-model="slot.values[field.name]"
          :container-class="field.containerClass"
          :rules="field.rules"
          :label="field.label"
          :name="field.name"
          :type="field.type"
          :options="field.options"
          :errors="!!slot.errors[field.name]"
          :disabled="field.disabled"
          :sub-label="field.subLabel"
          :bind="field.bind"
          :mask="field.mask"
          :searchable="field.searchable"
          :description="field.description"
          @open:options="$emit('open:options')"
          @close:options="$emit('close:options')"
        />
      </template>

      <slot v-bind="slot" name="default" />

      <div v-if="schema.buttons" class="col-12">
        <div :class="schema.buttonsContainer.class">
          <div
            v-for="(button, key) in schema.buttons"
            :key="key"
            :class="button.containerClass"
          >
            <x-btn
              v-if="button.type !== 'submit'"
              :label="button.label"
              color="gray-500"
              outline
              size="lg"
              class="w-100"
              no-caps
              no-wrap
              v-bind="button.bind"
              @click="button.action"
            />
            <x-btn
              v-else
              :label="button.label"
              :disabled="disableProceed"
              :loading="loading"
              :class="button.class"
              class="w-100 h-100 flex-row justify-content-center"
              color="primary"
              size="lg"
              no-caps
              no-wrap
              loading-normal
              v-bind="button.bind"
              @click="handleSubmit(slot)"
            />
          </div>
        </div>
      </div>
    </form>
  </FormComponent>
</template>

<script lang="ts">
import { FormSchema, HandleSubmit } from '@/types';
import { PropType, computed, defineComponent } from 'vue';
import { Form as FormComponent } from 'vee-validate';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XField from '@/components/x-form/XField.vue';

const config = {
  classError: 'is-invalid',
  classLabel: 'form-label',
  classLabelError: 'text-danger',
  classErrorMessage: 'validator-rule-error',
};
const getConfig = (key: keyof typeof config) => config[key];

class XFormFactory<T> {
  define() {
    return defineComponent({
      name: 'XForm',
      components: {
        XField,
        FormComponent,
        XBtn,
      },
      props: {
        schema: {
          type: Object as PropType<FormSchema>,
          default: () => ({
            fields: [],
            buttons: [],
            buttonsContainer: {},
          }),
        },
        initialValues: {
          type: Object,
          default: undefined,
        },
        unique: {
          type: String,
          default: Math.random().toString(36).substring(2, 9),
        },
        classError: {
          type: String,
          default: getConfig('classError'),
        },
        classLabel: {
          type: String,
          default: getConfig('classLabel'),
        },
        classLabelError: {
          type: String,
          default: getConfig('classLabelError'),
        },
        classErrorMessage: {
          type: String,
          default: getConfig('classErrorMessage'),
        },
        loading: Boolean,
        disableProceed: Boolean,
      },
      emits: ['submit', 'open:options', 'close:options'],
      setup(props, { emit }) {
        const iniValues = computed(() => {
          let values = {};
          props.schema.fields.forEach((s) => {
            values = {
              ...values,
              [s.name]: s.value,
            };
          });
          return {
            ...values,
            ...props.initialValues,
          };
        });

        const handleSubmit = (slot: { handleSubmit: HandleSubmit }) => {
          slot.handleSubmit(
            () => null,
            (values: Record<string, unknown>, opt) => {
              emit('submit', values as T, opt);
            }
          );
        };

        return {
          handleSubmit,
          iniValues,
        };
      },
    });
  }
}
const main = new XFormFactory().define();
export function GenericXForm<T>() {
  return main as ReturnType<XFormFactory<T>['define']>;
}
export default main;
</script>
