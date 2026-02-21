<template>
  <div>
    <dt class="mt-3">Posição</dt>
    <dd class="font-hind-18-primary font-weight-bold">
      #{{ preregistration.position }}
    </dd>
    <p class="text-size-10">
      A posição pode sofrer mudanças de acordo com os atendimentos de prioridade
      do município.
    </p>
  </div>
</template>

<script setup lang="ts">
import { PreRegistrationOverview } from '@/modules/preregistration/types';
import { ProtocolStatusPreRegistration } from '@/modules/protocol/types';

defineProps<{
  preregistration: ProtocolStatusPreRegistration | PreRegistrationOverview;
}>();
</script>
