<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\Services;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusAutoRejectedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Services\AutoRejectSummonedPreRegistrationsService;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class AutoRejectSummonedPreRegistrationsServiceTest extends TestCase
{
    public function test_auto_reject_summoned_preregistrations(): void
    {
        Event::fake([
            PreRegistrationStatusAutoRejectedEvent::class,
        ]);

        // Cria processo com auto reject configurado
        $process = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 5,
            'active' => true,
        ]);

        // Cria pré-matrículas convocadas
        $prereg1 = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(6), // Ultrapassou o prazo
        ]);

        $prereg2 = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(4), // Dentro do prazo
        ]);

        // Cria pré-matrícula de outro processo
        $otherProcess = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 5,
            'active' => true,
        ]);

        $prereg3 = PreRegistrationFactory::new()->create([
            'process_id' => $otherProcess->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(6), // Ultrapassou o prazo
        ]);

        // Executa o service
        $service = app(AutoRejectSummonedPreRegistrationsService::class);
        $service->rejectForProcess($process);
        $service->rejectForProcess($otherProcess);

        // Verifica que apenas a pré-matrícula que ultrapassou o prazo foi indeferida
        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg1->id,
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => 'Inscrição indeferida por vencimento do prazo de convocação',
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg2->id,
            'status' => PreRegistration::STATUS_SUMMONED,
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg3->id,
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => 'Inscrição indeferida por vencimento do prazo de convocação',
        ]);

        // Verifica se o evento foi disparado para as pré-inscrições rejeitadas
        Event::assertDispatched(PreRegistrationStatusAutoRejectedEvent::class, function ($event) use ($prereg1) {
            return $event->preRegistration->id === $prereg1->id
                && (int) $event->before === PreRegistration::STATUS_SUMMONED
                && $event->afterJustification === 'Inscrição indeferida por vencimento do prazo de convocação';
        });

        Event::assertDispatched(PreRegistrationStatusAutoRejectedEvent::class, function ($event) use ($prereg3) {
            return $event->preRegistration->id === $prereg3->id
                && (int) $event->before === PreRegistration::STATUS_SUMMONED
                && $event->afterJustification === 'Inscrição indeferida por vencimento do prazo de convocação';
        });

        // Verifica se o evento não foi disparado para a pré-inscrição que não foi rejeitada
        Event::assertNotDispatched(PreRegistrationStatusAutoRejectedEvent::class, function ($event) use ($prereg2) {
            return $event->preRegistration->id === $prereg2->id;
        });
    }

    public function test_does_not_reject_when_process_is_inactive(): void
    {
        // Cria processo inativo com auto reject configurado
        $process = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 5,
            'active' => false,
        ]);

        // Cria pré-matrícula convocada que ultrapassou o prazo
        $prereg = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(6),
        ]);

        // Executa o service
        $service = app(AutoRejectSummonedPreRegistrationsService::class);
        $service->rejectForProcess($process);

        // Verifica que a pré-matrícula não foi indeferida
        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg->id,
            'status' => PreRegistration::STATUS_SUMMONED,
        ]);
    }

    public function test_does_not_reject_when_auto_reject_is_disabled(): void
    {
        // Cria processo com auto reject desativado
        $process = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => false,
            'auto_reject_days' => 5,
            'active' => true,
        ]);

        // Cria pré-matrícula convocada que ultrapassou o prazo
        $prereg = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(6),
        ]);

        // Executa o service
        $service = app(AutoRejectSummonedPreRegistrationsService::class);
        $service->rejectForProcess($process);

        // Verifica que a pré-matrícula não foi indeferida
        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg->id,
            'status' => PreRegistration::STATUS_SUMMONED,
        ]);
    }

    public function test_auto_reject_only_when_summoned_at_is_exact_days(): void
    {
        // Cria processo com auto reject configurado
        $process = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 5,
            'active' => true,
        ]);

        // Cria pré-matrículas convocadas com diferentes datas
        $prereg1 = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(6), // 6 dias (deve ser indeferida)
        ]);

        $prereg2 = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(5), // 5 dias (não deve ser indeferida)
        ]);

        $prereg3 = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(4), // 4 dias (não deve ser indeferida)
        ]);

        // Executa o service
        $service = app(AutoRejectSummonedPreRegistrationsService::class);
        $service->rejectForProcess($process);

        // Verifica que apenas as pré-matrículas com mais de 5 dias foram indeferidas
        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg1->id,
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => 'Inscrição indeferida por vencimento do prazo de convocação',
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg2->id,
            'status' => PreRegistration::STATUS_SUMMONED,
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg3->id,
            'status' => PreRegistration::STATUS_SUMMONED,
        ]);
    }

    public function test_does_not_reject_when_summoned_at_is_null(): void
    {
        Event::fake([
            PreRegistrationStatusAutoRejectedEvent::class,
        ]);

        // Cria processo com auto reject configurado
        $process = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 5,
            'active' => true,
        ]);

        // Cria pré-matrícula convocada sem summoned_at
        $prereg1 = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => null,
        ]);

        // Cria pré-matrícula convocada com summoned_at
        $prereg2 = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(6), // Ultrapassou o prazo
        ]);

        // Executa o service
        $service = app(AutoRejectSummonedPreRegistrationsService::class);
        $service->rejectForProcess($process);

        // Verifica que a pré-matrícula sem summoned_at não foi indeferida
        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg1->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => null,
        ]);

        // Verifica que a pré-matrícula com summoned_at foi indeferida
        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg2->id,
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => 'Inscrição indeferida por vencimento do prazo de convocação',
        ]);

        // Verifica se o evento não foi disparado para a pré-inscrição sem summoned_at
        Event::assertNotDispatched(PreRegistrationStatusAutoRejectedEvent::class, function ($event) use ($prereg1) {
            return $event->preRegistration->id === $prereg1->id;
        });

        // Verifica se o evento foi disparado para a pré-inscrição com summoned_at
        Event::assertDispatched(PreRegistrationStatusAutoRejectedEvent::class, function ($event) use ($prereg2) {
            return $event->preRegistration->id === $prereg2->id
                && (int) $event->before === PreRegistration::STATUS_SUMMONED
                && $event->afterJustification === 'Inscrição indeferida por vencimento do prazo de convocação';
        });
    }

    public function test_get_processes_to_reject_returns_only_valid_processes(): void
    {
        // Cria processos válidos
        $process1 = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 5,
            'active' => true,
        ]);

        $process2 = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 10,
            'active' => true,
        ]);

        // Cria processos inválidos
        $inactiveProcess = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 5,
            'active' => false,
        ]);

        $disabledAutoRejectProcess = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => false,
            'auto_reject_days' => 5,
            'active' => true,
        ]);

        $nullDaysProcess = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => null,
            'active' => true,
        ]);

        // Executa o service
        $service = app(AutoRejectSummonedPreRegistrationsService::class);
        $processes = $service->getProcessesToReject();

        // Verifica que apenas os processos válidos foram retornados
        $this->assertCount(2, $processes);
        $this->assertTrue($processes->contains($process1));
        $this->assertTrue($processes->contains($process2));
        $this->assertFalse($processes->contains($inactiveProcess));
        $this->assertFalse($processes->contains($disabledAutoRejectProcess));
        $this->assertFalse($processes->contains($nullDaysProcess));
    }

    public function test_reject_all_processes_rejects_all_valid_processes(): void
    {
        Event::fake([
            PreRegistrationStatusAutoRejectedEvent::class,
        ]);

        // Cria processos válidos
        $process1 = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 5,
            'active' => true,
        ]);

        $process2 = ProcessFactory::new()->complete()->create([
            'auto_reject_by_days' => true,
            'auto_reject_days' => 10,
            'active' => true,
        ]);

        // Cria pré-matrículas convocadas que ultrapassaram o prazo
        $prereg1 = PreRegistrationFactory::new()->create([
            'process_id' => $process1->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(6),
        ]);

        $prereg2 = PreRegistrationFactory::new()->create([
            'process_id' => $process2->id,
            'status' => PreRegistration::STATUS_SUMMONED,
            'summoned_at' => now()->subDays(11),
        ]);

        // Executa o service
        $service = app(AutoRejectSummonedPreRegistrationsService::class);
        $service->rejectAllProcesses();

        // Verifica que as pré-matrículas foram indeferidas
        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg1->id,
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => 'Inscrição indeferida por vencimento do prazo de convocação',
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $prereg2->id,
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => 'Inscrição indeferida por vencimento do prazo de convocação',
        ]);

        // Verifica se os eventos foram disparados
        Event::assertDispatched(PreRegistrationStatusAutoRejectedEvent::class, function ($event) use ($prereg1) {
            return $event->preRegistration->id === $prereg1->id;
        });

        Event::assertDispatched(PreRegistrationStatusAutoRejectedEvent::class, function ($event) use ($prereg2) {
            return $event->preRegistration->id === $prereg2->id;
        });
    }
}
