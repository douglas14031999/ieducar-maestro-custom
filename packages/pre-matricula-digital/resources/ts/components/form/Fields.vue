<template>
  <div class="row">
    <x-field
      v-for="(field, index) in modelFields"
      :key="index"
      v-model="modelData[field.key]"
      :container-class="field.classes"
      :rules="formatRules(field.rules)"
      :label="field.field.name"
      :description="field.field.description"
      :name="field.key"
      :type="field.type"
      :options="field.field.options"
      :errors="!!errors[field.key]"
    />
  </div>
</template>

<script lang="ts">
import { Option, Rules } from '@/types';
import { PropType, defineComponent } from 'vue';
import { ParseFieldFromProcess } from '@/modules/preregistration/types';
import XField from '@/components/x-form/XField.vue';
import { useVModel } from '@vueuse/core';

class FieldsFactory<T> {
  define() {
    return defineComponent({
      name: 'Fields',
      components: {
        XField,
      },
      props: {
        data: {
          type: Object as PropType<T>,
          default: () => ({}),
        },
        fields: {
          type: Array as PropType<ParseFieldFromProcess[]>,
          default: () => [],
        },
        errors: {
          type: Object as PropType<Rules>,
          default: () => ({}),
        },
        cities: {
          type: Array as PropType<Option[]>,
          default: () => [],
        },
      },
      setup(props) {
        const modelFields = useVModel(props, 'fields');
        const modelData = useVModel(props, 'data') as unknown as {
          [key: string]: string | string[] | boolean | number;
        };

        const formatRules = (rules: Rules) => {
          if (typeof rules === 'string') return rules;
          const keys = Object.keys(rules);
          const ruleStr: string[] = [];
          keys.forEach((key) => {
            if (typeof rules[key] === 'boolean') {
              if (rules[key]) {
                ruleStr.push(key);
              }
            } else {
              ruleStr.push(`${key}:${rules[key]}`);
            }
          });
          return ruleStr.join('|');
        };

        return {
          modelFields,
          modelData,
          formatRules,
        };
      },
    });
  }
}

const main = new FieldsFactory().define();
export function GenericFields<T>() {
  return main as ReturnType<FieldsFactory<T>['define']>;
}
export default main;
</script>
