<template>
  <div style="display: none">
    <google-maps-marker-component
      v-for="marker in markers"
      :ref="gmarker"
      :key="marker.id"
      :map="map"
      :marker="marker"
      @click="$emit('click', $event)"
    >
      <slot name="default" :map="map" :marker="marker"></slot>
    </google-maps-marker-component>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { GoogleMapsMarker } from '@/types';
import GoogleMapsMarkerComponent from './GoogleMapsMarker.vue';

interface ChildrenNode {
  map: google.maps.Map;
  markers: GoogleMapsMarker[];
  marker: GoogleMapsMarker;
  internalMarker: google.maps.Marker;
}

defineEmits<{
  (action: 'click', payload: MouseEvent): void;
}>();

const props = defineProps<{
  map: google.maps.Map;
  markers: GoogleMapsMarker[];
}>();

const children = ref<ChildrenNode[]>([]);

const gmarker = (el: unknown) => {
  if (el) {
    children.value.push(el as unknown as ChildrenNode);
  }
};

const reactiveMarkers = computed(() => props.markers);

watch(
  reactiveMarkers,
  (newMarkers, oldMarkers) => {
    const newIds = newMarkers.map((marker) => marker.id);
    const oldIds = oldMarkers
      .map((marker) => marker.id)
      .filter((id) => !newIds.includes(id));

    children.value.forEach((component) => {
      if (oldIds.includes(component.marker.id)) {
        component.internalMarker.setVisible(false);
      }

      if (newIds.includes(component.marker.id)) {
        component.internalMarker.setVisible(true);
        component.internalMarker.setPosition(component.marker.position);
      }
    });
  },
  {
    deep: true,
  }
);
</script>
