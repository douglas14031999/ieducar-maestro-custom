/* eslint-disable @typescript-eslint/no-explicit-any */
/* eslint-disable @typescript-eslint/no-unused-vars */
/**
 * Pacote de integração com o Google Analytics.
 * @packageDocumentation
 * @module Analytics
 * @preferred
 * @author DavidWells
 * @url https://github.com/DavidWells/analytics
 * @license MIT
 * @version 1.0.10
 */

import Analytics, { AnalyticsInstance, PageData } from 'analytics';
import googleTagManager from '@analytics/google-tag-manager';

export const analytics = (): AnalyticsInstance => {
  const gtm = import.meta.env.VITE_GOOGLE_TAG_MANAGER;
  const appName = import.meta.env.VITE_APP_NAME;

  if (!gtm || !appName) {
    return {
      page: (
        data?: PageData,
        options?: any,
        callback?: (...params: any[]) => any
      ) => new Promise(() => null),
      ready: (callback: (...params: any[]) => any) => {
        return;
      },
    } as AnalyticsInstance;
  }

  return Analytics({
    app: appName,
    plugins: [
      googleTagManager({
        containerId: gtm,
      }),
    ],
  });
};
