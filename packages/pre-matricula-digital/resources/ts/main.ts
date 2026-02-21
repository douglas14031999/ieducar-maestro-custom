import '@/plugin/slugify';
import '@/plugin/sortby';
import '@/validator';
import '@/assets/sass/index.scss';
import 'v-tooltip/dist/v-tooltip.css';

import { Honeybadger, honeyBadgerConfig } from '@/packages';
import { RouterView } from 'vue-router';
import VTooltip from 'v-tooltip';
import { createApp } from 'vue';
import filters from '@/filters';
import { registerComponents } from '@/components';
import router from '@/router';
import store from '@/store';

const app = createApp(RouterView);

app.use(router);

app.use(store);

app.use(VTooltip);

app.provide('$filters', filters);

if (import.meta.env.MODE === 'production') {
  if (import.meta.env.VITE_HONEYBADGER_KEY) {
    app.use(Honeybadger, honeyBadgerConfig);
  }
  registerComponents(app);
}

app.mount('#app');

// Deploy
