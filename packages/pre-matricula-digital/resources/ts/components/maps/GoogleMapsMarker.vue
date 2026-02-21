<template>
  <div style="display: none">
    <slot name="default"></slot>
  </div>
</template>

<script setup lang="ts">
import {
  ComponentInternalInstance,
  computed,
  getCurrentInstance,
  onBeforeUnmount,
  onMounted,
  ref,
  watch,
} from 'vue';
import { GoogleMapsMarker, LatLng } from '@/types';

const emit = defineEmits<{
  (action: 'new-position', payload: LatLng): void;
}>();

const props = defineProps<{
  map: google.maps.Map;
  marker: GoogleMapsMarker;
  draggable?: boolean;
}>();

const vm = getCurrentInstance() as ComponentInternalInstance;
const internalMarker = ref<google.maps.Marker>();

const draggableConfig = {
  icon: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
};

const config = computed(() => {
  const dConfig = props.draggable ? draggableConfig : {};

  return {
    ...(props.marker.config || {}),
    ...dConfig,
  };
});

onMounted(() => {
  internalMarker.value = new google.maps.Marker({
    position: props.marker.position,
    map: props.map,
    draggable: props.draggable,
    ...config.value,
  });

  if (vm.proxy?.$el.innerHTML.length) {
    const infowindow = new google.maps.InfoWindow({
      content: vm.proxy?.$el.innerHTML,
    });

    internalMarker.value?.addListener('click', () => {
      infowindow.open(props.map, internalMarker.value);
    });
  }

  internalMarker.value?.addListener('click', () => {
    vm.proxy?.$emit('click', props.marker);
  });

  internalMarker.value?.addListener(
    'dragend',
    (event: { latLng: google.maps.LatLng }) => {
      emit('new-position', {
        lat: event.latLng.lat(),
        lng: event.latLng.lng(),
      });
    }
  );

  if (props.draggable) {
    google.maps.event.addListener(
      props.map,
      'click',
      (event: { latLng: google.maps.LatLng }) => {
        internalMarker.value?.setPosition(event.latLng);
        emit('new-position', {
          lat: event.latLng.lat(),
          lng: event.latLng.lng(),
        });
      }
    );
  }
});

const reactiveLat = computed(() => props.marker.position?.lat);
const reactiveLng = computed(() => props.marker.position?.lng);

watch(
  [reactiveLat, reactiveLng],
  () => {
    internalMarker.value?.setPosition(props.marker.position);
  },
  { deep: true }
);

onBeforeUnmount(() => {
  internalMarker.value?.setVisible(false);
});

defineExpose({
  internalMarker,
  marker: props.marker,
});
</script>
