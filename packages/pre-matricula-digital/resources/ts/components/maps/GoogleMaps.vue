<template>
  <div class="google-map">
    <div ref="map" class="google-map-container"></div>
    <template v-if="Boolean(googleInstance) && Boolean(mapInstance)">
      <slot name="default" :map="mapInstance"></slot>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';

const props = withDefaults(
  defineProps<{
    config: Record<string, unknown>;
    lat: number;
    lng: number;
    zoom: number;
  }>(),
  {
    config: () => ({}),
    lat: 0,
    lng: 0,
    zoom: 10,
  }
);

const googleInstance = ref<typeof window.google>();
const map = ref<HTMLElement>();
const mapInstance = ref<google.maps.Map>();

const initialize = (googleParam: typeof window.google) => {
  googleInstance.value = googleParam;

  const config = Object.assign(props.config, {
    center: {
      lat: props.lat,
      lng: props.lng,
    },
    zoom: props.zoom,
  });

  mapInstance.value = new googleInstance.value.maps.Map(
    map.value as HTMLElement,
    config
  );
};

const setCenter = () => {
  mapInstance.value?.setCenter({
    lat: props.lat,
    lng: props.lng,
  });
};

const reactiveLat = computed(() => props.lat);
const reactiveLng = computed(() => props.lng);

watch([reactiveLat, reactiveLng], () => {
  setCenter();
});

onMounted(() => {
  initialize(window.google);
});
</script>

<style scoped>
@keyframes spinner {
  to {
    transform: rotate(360deg);
  }
}

.google-map {
  position: relative;
}

.google-map::before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  width: 30px;
  height: 30px;
  margin-top: -15px;
  margin-left: -15px;
  border-radius: 50%;
  border: 1px solid #ccc;
  border-top-color: #07d;
  animation: spinner 0.6s linear infinite;
}

.google-map-container {
  min-height: 100%;
  width: 100%;
}
</style>
