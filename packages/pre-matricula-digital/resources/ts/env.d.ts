/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_HONEYBADGER_KEY: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}

declare module '*.vue' {
  import type { DefineComponent } from 'vue';
  // eslint-disable-next-line @typescript-eslint/no-explicit-any, @typescript-eslint/ban-types
  const component: DefineComponent<{}, {}, any>;
  export default component;
}

declare module 'v-tooltip';
declare module 'vue-the-mask';
declare module 'vue-flatpickr-component';
declare module 'vue-select';
declare module 'froala-editor';
declare module '@analytics/google-tag-manager';
declare module 'honeybadger-vue3-composition-api';
