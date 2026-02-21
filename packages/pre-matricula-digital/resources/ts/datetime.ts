import moment from 'moment';

moment.locale('pt-BR');

const getFormattedDate = (date: string, rawFormat = 'YYYY-MM-DD'): string =>
  moment(date, rawFormat).format('DD/MM/YYYY');

const getRawDate = (date: string, prettyFormat = 'DD/MM/YYYY'): string =>
  moment(date, prettyFormat).format('YYYY-MM-DD');

const getDate = (date: string, rawFormat = 'YYYY-MM-DD'): string =>
  moment(date, rawFormat).format('YYYY-MM-DD');

const getFormattedDateTime = (date: string): string =>
  moment(date).format('DD/MM/YYYY [às] HH:mm');

const getFormattedTime = (date: string): string => moment(date).format('HH:mm');

const getFormattedYearFromNow = (): string => moment().format('YYYY');

const isDateAfter = (date: string, after: string): boolean =>
  moment(date, 'YYYY-MM-DD', true).isAfter(moment(after, 'YYYY-MM-DD', true));

const isBefore = (date: string, days: number): boolean =>
  moment(date, 'YYYY-MM-DD', true).isBefore(moment().subtract(days, 'days'));

const isDateBetween = (date: string, start: string, end: string) =>
  moment(date).isBetween(moment(start), moment(end), undefined, '[]');

const isValidDate = (date: string): boolean =>
  moment(date, 'YYYY-MM-DD', true).isValid();

const isValidTime = (date: string): boolean =>
  moment(date, 'HH:mm', true).isValid();

const isValidBornDate = (date: string): boolean => {
  return (
    isValidDate(date) &&
    moment(date).year() > 1900 &&
    moment().diff(moment(date)) >= 0
  );
};

const getYear = (date: string): number => moment(date).year();

export {
  getFormattedDate,
  getRawDate,
  getDate,
  getFormattedDateTime,
  getFormattedTime,
  getFormattedYearFromNow,
  isBefore,
  isDateAfter,
  isDateBetween,
  isValidDate,
  isValidTime,
  isValidBornDate,
  getYear,
};
