import { RouteRecordRaw } from 'vue-router';

const modules = import.meta.glob('../modules/*/routes.ts', { eager: true });
const submodules = import.meta.glob('../modules/*/*/routes.ts', {
  eager: true,
});

const routes: RouteRecordRaw[] = [];

Object.entries(modules)
  .concat(Object.entries(submodules))
  .forEach(([, definition]) => {
    routes.push(...(definition as { default: RouteRecordRaw[] }).default);
  });

export default routes;
