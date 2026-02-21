<template>
  <x-form @submit="submit">
    <template #default="slot">
      <div class="col-12">
        <x-card bordered>
          <x-card-section class="pl-4 pr-4 pt-3 p-0">
            <h5 class="text-h5 text-primary">
              {{ title }}
            </h5>
          </x-card-section>
          <x-card-section class="pl-4 pr-4 pb-3 p-0">
            <p class="mb-0 text-black-80 text-size-10 font-hind text-uppercase">
              Insira o número do protocolo
            </p>

            <div class="row">
              <div class="col-12 col-md-8 mb-md-0">
                <Field
                  v-slot="{ field }"
                  as="div"
                  rules="required"
                  class="position-relative mb-3"
                  name="protocol"
                >
                  <input
                    v-bind="field"
                    class="form-control pl-4 mr-3"
                    :class="{ 'is-invalid': !!slot.errors['protocol'] }"
                  />
                  <span class="protocol-hashtag">#</span>

                  <ErrorMessage name="protocol" class="validator-rule-error" />
                </Field>
              </div>
              <div class="col-12 col-md-4 pt-3 pt-md-0">
                <x-btn
                  label="Pesquisar"
                  color="primary"
                  type="submit"
                  no-caps
                  no-wrap
                  class="w-100"
                  style="font-size: 1.2em"
                />
              </div>
            </div>
            <p class="text-muted pt-3 pt-md-0">
              <router-link
                to="/onde-encontro-o-protocolo"
                class="text-black-80 font-hind text-underline mb-0"
              >
                Onde encontro o protocolo?
              </router-link>
            </p>
          </x-card-section>
        </x-card>
      </div>
    </template>
  </x-form>
</template>

<script setup lang="ts">
import { ErrorMessage, Field } from 'vee-validate';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XForm from '@/components/x-form/XForm.vue';
import { useRouter } from 'vue-router';

withDefaults(
  defineProps<{
    title: string;
  }>(),
  {
    title: '',
  }
);

const router = useRouter();

const submit = (model: { protocol: string }) => {
  router.push({
    name: 'protocol.status',
    params: {
      id: model.protocol,
    },
  });
};
</script>
