import { VacancyLoadProcess, VacancyLoadProcessAnalyse } from './types';

export const trunc = (value: number) => Math.trunc(value * 10000) / 100;

export const analyse = (p: VacancyLoadProcess): VacancyLoadProcessAnalyse => {
  const process = {
    ...p,
    unavailable: 0,
    exceded: 0,
    ultrapassed: 0,
    percentual: {
      accepted: 0,
      available: 0,
      unavailable: 0,
      exceded: 0,
      totalExceded: 0,
      total: 0,
      waiting: 0,
      waitingRealAvailable: 0,
      waitingExceded: 0,
      waitingAvailable: 0,
    },
  } as VacancyLoadProcessAnalyse;

  // Se o número de vagas disponíveis for negativo, quer dizer que as vagas foram excedidas
  if (process.available < 0) {
    process.exceded = Math.abs(process.available);
    process.available = 0;
  }

  // Se o número de vagas aceitas for maior que o total de vagas, quer dizer que os limites foram ultrapassados
  if (process.accepted > process.total) {
    process.ultrapassed = process.accepted - process.total;
  }

  process.unavailable = process.total - process.accepted - process.available;
  process.max = process.total + process.exceded + process.ultrapassed;
  process.totalExceded = process.exceded + process.ultrapassed;

  process.percentual.total = trunc(process.total / process.max);
  process.percentual.totalExceded = trunc(process.totalExceded / process.max);
  process.percentual.accepted = trunc(process.accepted / process.max);
  process.percentual.available = trunc(process.available / process.max);
  process.percentual.unavailable = trunc(process.unavailable / process.max);
  process.percentual.exceded = trunc(process.exceded / process.max);

  process.waitingAvailable = process.available - process.waiting;
  process.waitingExceded = 0;

  if (process.waitingAvailable < 0) {
    process.waitingExceded = Math.abs(process.waitingAvailable);
    process.waitingAvailable = 0;
  }

  process.maxWaiting = Math.max(process.available, process.waiting);
  process.percentual.waiting = trunc(process.waiting / process.maxWaiting);
  process.percentual.waitingRealAvailable = trunc(
    process.waitingAvailable / process.maxWaiting
  );
  process.percentual.waitingAvailable = trunc(
    process.available / process.maxWaiting
  );
  process.percentual.waitingExceded = trunc(
    process.waitingExceded / process.maxWaiting
  );

  return process;
};
