import { App } from 'vue';

const allowedComponents = ['XForm', 'Fields'];

export const registerComponents = (app: App<Element>) => {
  const components = import.meta.glob('./**/*.vue', { eager: true });

  Object.entries(components).forEach(([path, definition]) => {
    const componentName = path
      .split('/')
      .pop()
      ?.replace(/\.\w+$/, '');

    if (componentName && allowedComponents.includes(componentName)) {
      // Register component on this Vue instance
      app.component(
        componentName,
        (definition as { default: any }).default
      );
    }
  });
};
