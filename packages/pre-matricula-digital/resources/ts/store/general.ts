import { Course, Grade, Period, SchoolYear } from '@/modules/processes/types';
import { Nullable, Option, Processes } from '@/types';
import { School } from '@/modules/school/types';
import axios from 'axios';
import { defineStore } from 'pinia';

declare global {
  interface Window {
    config: {
      city: string;
      state: string;
      ibge_codes: string;
      map: {
        lat: number;
        lng: number;
        zoom: number;
      };
      token: string;
      logo: string;
      slogan: string;
      allow_optional_address: boolean;
      show_how_to_do_video: boolean;
      video_intro_url: string;
      link_to_restrict_area: string;
      features: {
        allow_preregistration_data_update: boolean;
        allow_external_system_data_update: boolean;
        allow_transfer_registration: boolean;
        transfer_description: string;
        allow_vacancy_certificate: boolean;
      };
    };
  }
}

type MapCenter = {
  lat: number;
  lng: number;
};

type MapConfigStyleStyler = {
  visibility: string;
};

type MapConfigStyle = {
  featureType: string;
  stylers: MapConfigStyleStyler[];
};

type MapConfig = {
  clickableIcons: boolean;
  streetViewControl: boolean;
  panControlOptions: boolean;
  mapTypeControl: boolean;
  styles: MapConfigStyle[];
};

type Map = {
  config: MapConfig;
  center: MapCenter;
  zoom: number;
};

type Entity = {
  city: string;
  state: string;
  ibgeCodes: number[];
};

type Config = {
  ['allow_optional_address']: boolean;
  ['show_how_to_do_video']: boolean;
  ['video_intro_url']: string;
  ['link_to_restrict_area']: string;
};

export type User = {
  name: string;
  level: number;
  schools: string[];
};

type Auth = {
  authenticated: boolean;
  checked: boolean;
  user: Nullable<User>;
};

interface GeneralState {
  auth: Auth;
  map: Map;
  entity: Entity;
  logo: string;
  slogan: string;
  config: Config;
  features: {
    allowPreregistrationDataUpdate: boolean;
    allowExternalSystemDataUpdate: boolean;
    allowTransferRegistration: boolean;
    transferDescription: string;
    allowVacancyCertificate: boolean;
  };
  cities: Option[];
  stageTypes: Option[];
  relationTypes: Option[];
  restrictionTypes: Option[];
  processes: Processes[];
  schools: School[];
  courses: Course[];
  grades: Grade[];
  periods: Period[];
  schoolYears: SchoolYear[];
}

export const useGeneralStore = defineStore('general', {
  state: (): GeneralState => ({
    auth: {
      authenticated: false,
      checked: false,
      user: null,
    },
    map: {
      config: {
        clickableIcons: false,
        streetViewControl: false,
        panControlOptions: false,
        mapTypeControl: false,
        styles: [
          {
            featureType: 'poi',
            stylers: [{ visibility: 'off' }],
          },
        ],
      },
      center: {
        lat: window.config.map.lat,
        lng: window.config.map.lng,
      },
      zoom: window.config.map.zoom,
    },
    entity: {
      city: window.config.city,
      state: window.config.state,
      ibgeCodes: window.config.ibge_codes
        .split(',')
        .filter((f: string) => f)
        .map((m: string) => Number(m)),
    },
    logo: window.config.logo,
    slogan: window.config.slogan,
    config: {
      allow_optional_address: window.config.allow_optional_address,
      show_how_to_do_video: window.config.show_how_to_do_video,
      video_intro_url: window.config.video_intro_url,
      link_to_restrict_area: window.config.link_to_restrict_area,
    },
    features: {
      allowPreregistrationDataUpdate:
        window.config.features.allow_preregistration_data_update,
      allowExternalSystemDataUpdate:
        window.config.features.allow_external_system_data_update,
      allowTransferRegistration:
        window.config.features.allow_transfer_registration,
      transferDescription: window.config.features.transfer_description,
      allowVacancyCertificate: window.config.features.allow_vacancy_certificate,
    },
    processes: [],
    schools: [],
    courses: [],
    grades: [],
    periods: [],
    schoolYears: [],
    stageTypes: [
      {
        key: 'REGISTRATION',
        label: 'Matrícula',
      },
      {
        key: 'REGISTRATION_RENEWAL',
        label: 'Rematrícula',
      },
      {
        key: 'WAITING_LIST',
        label: 'Lista de espera',
      },
    ],
    relationTypes: [
      {
        key: 'MOTHER',
        label: 'Mãe',
      },
      {
        key: 'FATHER',
        label: 'Pai',
      },
      {
        key: 'GUARDIAN',
        label: 'Responsável Legal',
      },
      {
        key: 'SELF',
        label: 'O próprio (você é o(a) aluno(a))',
      },
    ],
    restrictionTypes: [
      {
        key: 'NONE',
        label: 'Todas',
        subLabel: 'Não haverá restrição para se inscrever',
      },
      {
        key: 'REGISTRATION_LAST_YEAR',
        label: 'Matrícula ativa no ano anterior ao processo',
        subLabel:
          'Apenas alunos(as) com matrícula ativa ao ano anterior do processo poderão se inscrever (rematrícula)',
      },
      {
        key: 'REGISTRATION_CURRENT_YEAR',
        label: 'Matrícula ativa no ano do processo',
        subLabel:
          'Apenas alunos(as) com matrícula ativa no ano do processo poderão se inscrever (remanejamentos/trocas)',
      },
      {
        key: 'NO_REGISTRATION_CURRENT_YEAR',
        label: 'Sem matrícula ativa no ano do processo',
        subLabel:
          'Apenas alunos(as) sem matrícula ativa no ano do processo poderão se inscrever (retorno)',
      },
      {
        key: 'NO_REGISTRATION_PERIOD_CURRENT_YEAR',
        label: 'Sem matrícula ativa no turno e ano do processo',
        subLabel:
          'Apenas alunos(as) sem matrícula ativa no(s) turno(s) e ano do processo poderão se inscrever',
      },
      {
        key: 'NEW_STUDENT',
        label: 'Novos alunos',
        subLabel:
          'Apenas alunos(as) sem cadastro no sistema poderão se inscrever (novo aluno)',
      },
    ],
    cities: [],
  }),
  getters: {
    isAdmin: (state: GeneralState) => state.auth.user?.level === 1,
    isCoordinator: (state: GeneralState) => state.auth.user?.level === 2,
    isSecretary: (state: GeneralState) => state.auth.user?.level === 4,
    getUserSchools: (state: GeneralState) =>
      state.auth.user ? state.auth.user.schools : [],
    getStageTypes: (state: GeneralState) => state.stageTypes,
    getRestrictionTypes: (state: GeneralState) => state.restrictionTypes,
    isProduction: () => process.env.NODE_ENV === 'production',
    isAuthenticated: (state: GeneralState) => state.auth.authenticated,
    getAuthenticatedUser: (state: GeneralState) => state.auth.user,
    getEntityData: (state: GeneralState) => state.entity,
    getSlogan: (state: GeneralState) => state.slogan,
    getLogo: (state: GeneralState) => state.logo,
    getCities: (state: GeneralState) => state.cities,
    getConfig: (state: GeneralState) => state.config,
  },
  actions: {
    authenticated(user: User) {
      this.auth.authenticated = true;
      this.auth.checked = true;
      this.auth.user = user;
    },
    checked() {
      this.auth.checked = true;
    },
    setProcesses(payload: Processes[]) {
      this.processes = payload;
    },
    setSchools(payload: School[]) {
      this.schools = payload;
    },
    addCities(payload: Option[]) {
      const cities = this.cities.concat(payload);
      // Adiciona a cidade no array de cidades, removendo duplicatas
      this.cities = cities.filter(
        (obj, key, array) =>
          key ===
          array.findIndex(
            (arr) => arr.key === obj.key && arr.label === obj.label
          )
      );
    },
    check() {
      return axios.get('/auth/check').then(({ data }) => {
        this.authenticated(data);
      });
    },
  },
  persist: false,
});
