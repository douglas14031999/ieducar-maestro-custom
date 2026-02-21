<template>
  <div>
    <div class="row mt-3">
      <header-entity-data />
    </div>
    <div class="row">
      <div class="col-12">
        <h2 class="title-find-school">Consultar escola</h2>
        <x-card flat class="bg-primary mt-4">
          <x-card-section>
            <form @submit.prevent="searchAddress">
              <x-field
                v-model="address"
                label="Endereço"
                label-class="form-label-bg-blue"
                name="address"
                type="TEXT"
                placeholder="Digite o seu endereço (rua e número)"
                container-class="w-100"
              />

              <div class="mt-2">
                <div class="d-flex justify-content-between">
                  <x-field
                    id="grade"
                    v-model="grade"
                    label="Série"
                    label-class="form-label-bg-blue"
                    name="grade"
                    type="SELECT"
                    :options="grades"
                    placeholder="Selecione a série"
                    container-class="mr-3 w-100"
                    searchable
                  />
                  <div class="d-flex align-items-center mt-3">
                    <x-btn
                      data-test="btn-search-school"
                      :loading="loadingSearchAddress"
                      type="submit"
                      icon="fa-search"
                      class="w-100 bg-primary-light text-primary flex-row"
                      style="height: 45px"
                      loading-normal
                    />
                  </div>
                </div>
              </div>
            </form>
          </x-card-section>
        </x-card>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mt-5">
        <div class="card">
          <google-maps
            :config="getMapConfig"
            :lat="lat"
            :lng="lng"
            :zoom="zoom"
            style="height: 400px"
          >
            <template #default="{ map }">
              <google-maps-marker-component
                v-if="marker.position"
                :map="(map as google.maps.Map)"
                :marker="marker"
              >
                <template #default>
                  <strong>{{ marker.title }}</strong>
                </template>
              </google-maps-marker-component>
              <google-maps-markers
                :markers="schoolsInMap"
                :map="(map as google.maps.Map)"
              >
                <template #default="{ marker: markerSlot }">
                  <div>
                    <div class="col-12 small text-uppercase">Escola</div>
                    <div class="col-12">{{ markerSlot.name }}</div>
                    <div class="col-12 small text-uppercase mt-2">Telefone</div>
                    <div class="col-12">
                      {{ `(${markerSlot.area_code}) ${markerSlot.phone}` }}
                    </div>
                  </div>
                </template>
              </google-maps-markers>
            </template>
          </google-maps>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  GradeFiltered,
  Processes,
  School,
  ShowVacanciesReturn,
  Vacancies,
} from '@/modules/school/types';
import { computed, onMounted, ref } from 'vue';
import GoogleMaps from '@/components/maps/GoogleMaps.vue';
import { GoogleMapsMarker } from '@/types';
import GoogleMapsMarkerComponent from '@/components/maps/GoogleMapsMarker.vue';
import GoogleMapsMarkers from '@/components/maps/GoogleMapsMarkers.vue';
import HeaderEntityData from '@/components/elements/HeaderEntityData.vue';
import { Schoolfind as SchoolFind } from '@/modules/school/api';
import XBtn from '@/components/elements/buttons/XBtn.vue';
import XCard from '@/components/elements/cards/XCard.vue';
import XCardSection from '@/components/elements/cards/XCardSection.vue';
import XField from '@/components/x-form/XField.vue';
import { markerAddress } from '@/util';
import { useGeneralStore } from '@/store/general';
import { useLoader } from '@/composables';

const { loader: loaderProcesses, data: processes } = useLoader<Processes[]>([]);
const { loader: loaderVacancies } = useLoader<ShowVacanciesReturn>();

const vacancies = ref<Vacancies[]>([]);
const schools = ref<School[]>([]);
const store = useGeneralStore();
const getMapConfig = computed(() => store.map.config);
const lat = ref<number | undefined>(store.map.center.lat);
const lng = ref<number | undefined>(store.map.center.lng);
const zoom = ref(store.map.zoom);
const loadingSearchAddress = ref<boolean | undefined>(false);
const address = ref(null);
const marker = ref<GoogleMapsMarker>(markerAddress());

const grade = ref<string>();
const geocoder = ref<google.maps.Geocoder>();

const grades = computed(() => {
  const grades: GradeFiltered[] = [];
  const unique: string[] = [];

  processes.value.forEach((process) => {
    process.grades.forEach((grade) => {
      if (unique.indexOf(grade.id) !== -1) {
        return;
      }

      unique.push(grade.id);
      grades.push({
        key: grade.id,
        label: grade.name,
      });
    });
  });

  return grades.sort((a: GradeFiltered, b: GradeFiltered) =>
    a.label > b.label ? 1 : -1
  );
});

const schoolsInMap = computed<GoogleMapsMarker[]>(() =>
  schools.value
    .filter((school) => school.id)
    .filter((school) => {
      const filteredVacancies = vacancies.value.filter(
        (v) => Number(v.school) === Number(school.id)
      );
      if (grade.value) {
        return filteredVacancies.find(
          (v) => Number(v.grade) === Number(grade.value)
        );
      }
      return true;
    })
    .filter((school) => school.lat && school.lng)
    .map((school) => ({
      ...school,
      title: school.name,
      position: new google.maps.LatLng(school.lat, school.lng),
    }))
);

const getData = () => {
  loaderProcesses(() => SchoolFind.showProcesses()).then((response) => {
    loaderVacancies(() =>
      SchoolFind.showVacancies({ processes: response.map((p) => p.id) })
    ).then((res) => {
      vacancies.value = res.vacancies;
      schools.value = res.schools;
    });
  });
};
getData();

const searchAddress: () => void = () => {
  if (!address.value) {
    return;
  }
  const filteredAddress = [address.value, store.entity.city, store.entity.state]
    .filter((i) => i)
    .join(', ');
  loadingSearchAddress.value = true;
  geocoder.value
    ?.geocode(
      { address: filteredAddress },
      (
        results: google.maps.GeocoderResult[] | null,
        status: google.maps.GeocoderStatus
      ) => {
        if (status === 'OK' && results) {
          lat.value = results[0].geometry.location.lat();
          lng.value = results[0].geometry.location.lng();
          marker.value.position = results[0].geometry.location;
        }
      }
    )
    .finally(() => {
      loadingSearchAddress.value = false;
    });
};

onMounted(() => {
  geocoder.value = new google.maps.Geocoder();
});
</script>

<style>
#google-map-index .google-map {
  height: 400px;
}
</style>
