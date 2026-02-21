import { Address, GoogleMapsMarker } from '@/types';
import {
  getFormattedDate,
  getFormattedDateTime,
  getFormattedTime,
} from '@/datetime';
import {
  joinAddress,
  joinAddressLine1,
  joinAddressLine2,
  joinAddressLine3,
  markerAddress,
} from '@/util';

export interface Filters {
  formatDateTime: (date: string) => string;
  formatDate: (date: string) => string;
  formatTime: (date: string) => string;
  joinAddress: (address: Address) => string;
  joinAddressLine1: (address: Address) => string;
  joinAddressLine2: (address: Address) => string;
  joinAddressLine3: (address: Address) => string;
  markerAddress: () => GoogleMapsMarker;
}

const filters: Filters = {
  formatDateTime(date: string) {
    return getFormattedDateTime(date);
  },
  formatDate(date: string) {
    return getFormattedDate(date);
  },
  formatTime(date: string) {
    return getFormattedTime(date);
  },
  joinAddress,
  joinAddressLine1,
  joinAddressLine2,
  joinAddressLine3,
  markerAddress,
};

export default filters;
