import {
  RouteRecordRaw,
  Router,
  createRouter,
  createWebHistory,
} from 'vue-router';

import { analytics } from '@/packages';
import auto from '@/router/auto';
import base from '@/router/base';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/Default.vue'),
    children: auto,
  },
];

const router: Router = createRouter({
  history: createWebHistory(base),
  scrollBehavior() {
    return { top: 0 };
  },
  routes,
});

router.afterEach((to) => {
  const analyticsInstance = analytics();

  analyticsInstance.ready(() => {
    analyticsInstance.page({
      path: to.path,
    });
  });

  document.title = to.meta.name
    ? `${to.meta.name} | Pré-Matrícula Digital`
    : 'Pré-Matrícula Digital';
});

export default router;
