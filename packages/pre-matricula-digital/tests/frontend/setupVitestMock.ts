import { vi } from 'vitest';

vi.mock('@/packages');

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
  };
  gtag: {
    apply: (window: Window & typeof globalThis, args: any[]) => void;
  };
  features: {
    allowPreregistrationDataUpdate: boolean;
    allowExternalSystemDataUpdate: boolean;
  };
  alert(message?: any): void;
}

(window as Window).config = {
  city: 'Some City',
  state: 'MS',
  ibge_codes: '02931',
  map: {
    lat: 0,
    lng: 0,
    zoom: 13,
  },
  token: 'ABCDE',
  logo: 'https://logo.com',
  slogan: 'Some slogan',
  allow_optional_address: false,
  show_how_to_do_video: true,
  video_intro_url: '',
  features: {
    allowPreregistrationDataUpdate: true,
    allowExternalSystemDataUpdate: true,
  },
};

/**
 * This function is necessary to mock the gtag function because
 * the gtag function is not available in the window object.
 */
window.gtag = {
  apply: (window: Window & typeof globalThis, args: any[]) => {
    return;
  },
};

window.alert = (message) => message;
