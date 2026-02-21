<?php

namespace iEducar\Packages\PreMatricula\Tests\Unit;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\UserFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PreRegistrationTimelineTest extends TestCase
{
    public function test_multiple_timeline_entries(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        // Primeira atualização
        event(new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_ACCEPTED,
            beforeJustification: 'Justificativa anterior',
            afterJustification: 'Justificativa nova'
        ));

        // Segunda atualização
        event(new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_ACCEPTED,
            after: PreRegistration::STATUS_REJECTED,
            beforeJustification: 'Justificativa anterior',
            afterJustification: 'Justificativa nova'
        ));

        // Verifica se foram criadas duas entradas na timeline
        $timelines = Timeline::where('model_id', $preRegistration->id)->get();
        $this->assertCount(2, $timelines);

        // Verifica a ordem cronológica
        $firstTimeline = $timelines->first();
        $lastTimeline = $timelines->last();

        // payload
        $this->assertEquals('Aguardando', $firstTimeline->payload['before']['status']);
        $this->assertEquals('Aceito', $firstTimeline->payload['after']['status']);
        $this->assertEquals('Aceito', $lastTimeline->payload['before']['status']);
        $this->assertEquals('Rejeitado', $lastTimeline->payload['after']['status']);
    }

    public function test_no_timeline_entry_when_status_not_changed_with_justification(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        // Atualiza apenas a justificativa, mantendo o mesmo status
        $event = new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_WAITING,
            beforeJustification: 'Justificativa anterior',
            afterJustification: 'Justificativa nova'
        );

        // Dispara o evento
        event($event);

        // Verifica se foi criada uma entrada na timeline
        $this->assertDatabaseHas('timelines', [
            'model_id' => $preRegistration->id,
            'model_type' => get_class($preRegistration),
            'type' => 'preregistration-status-updated',
        ]);

        // Verifica se o payload contém apenas as justificativas
        $timeline = Timeline::first();
        $this->assertNotNull($timeline);
        $this->assertEquals('Justificativa anterior', $timeline->payload['before']['justification']);
        $this->assertEquals('Justificativa nova', $timeline->payload['after']['justification']);
        $this->assertArrayNotHasKey('status', $timeline->payload['before']);
        $this->assertArrayNotHasKey('status', $timeline->payload['after']);
    }

    public function test_no_timeline_entry_when_status_changed_without_justification(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        // Atualização sem justificativa
        event(new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_ACCEPTED,
            beforeJustification: null,
            afterJustification: null
        ));

        $timeline = Timeline::first();
        $this->assertNotNull($timeline);
        $this->assertEquals('Aguardando', $timeline->payload['before']['status']);
        $this->assertEquals('Aceito', $timeline->payload['after']['status']);
        $this->assertArrayNotHasKey('justification', $timeline->payload['before']);
        $this->assertArrayNotHasKey('justification', $timeline->payload['after']);
    }

    public function test_no_timeline_entry_when_all_values_are_null(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        // Tenta atualizar com todos os valores nulos
        $event = new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_WAITING,
            beforeJustification: null,
            afterJustification: null
        );

        // Dispara o evento
        event($event);

        // Verifica se não foi criada nenhuma entrada na timeline
        $this->assertDatabaseMissing('timelines', [
            'model_id' => $preRegistration->id,
            'model_type' => get_class($preRegistration),
            'type' => 'preregistration-status-updated',
        ]);
    }

    public function test_timeline_entry_with_partial_justification(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        // Atualização com apenas uma justificativa
        event(new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_ACCEPTED,
            beforeJustification: 'Justificativa anterior',
            afterJustification: null
        ));

        $timeline = Timeline::first();
        $this->assertNotNull($timeline);
        $this->assertEquals('Justificativa anterior', $timeline->payload['before']['justification']);
        $this->assertNull($timeline->payload['after']['justification']);
    }

    public function test_timeline_entry_with_unknown_status(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        // Atualização com status desconhecido
        event(new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: 999, // Status desconhecido
            after: PreRegistration::STATUS_ACCEPTED,
            beforeJustification: 'Justificativa anterior',
            afterJustification: 'Justificativa nova'
        ));

        $timeline = Timeline::first();
        $this->assertNotNull($timeline);
        $this->assertEquals('Desconhecido', $timeline->payload['before']['status']);
        $this->assertEquals('Aceito', $timeline->payload['after']['status']);
    }

    public function test_timeline_entry_with_user_info(): void
    {
        $user = UserFactory::new()->create();
        Auth::login($user);

        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_ACCEPTED,
            beforeJustification: 'Justificativa anterior',
            afterJustification: 'Justificativa nova'
        );

        // Dispara o evento
        event($event);

        // Verifica se foi criada uma entrada na timeline
        $this->assertDatabaseHas('timelines', [
            'model_id' => $preRegistration->id,
            'model_type' => get_class($preRegistration),
            'type' => 'preregistration-status-updated',
        ]);

        // Verifica se o payload contém as informações do usuário
        $timeline = Timeline::first();
        $this->assertNotNull($timeline);
        $this->assertIsArray($timeline->payload['user']);
        $this->assertEquals($user->id, $timeline->payload['user']['id']);
        $this->assertEquals($user->name, $timeline->payload['user']['name']);
    }

    public function test_timeline_entry_without_user(): void
    {
        Auth::logout();

        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_ACCEPTED,
            beforeJustification: 'Justificativa anterior',
            afterJustification: 'Justificativa nova'
        );

        // Dispara o evento
        event($event);

        // Verifica se foi criada uma entrada na timeline
        $this->assertDatabaseHas('timelines', [
            'model_id' => $preRegistration->id,
            'model_type' => get_class($preRegistration),
            'type' => 'preregistration-status-updated',
        ]);

        // Verifica se o payload não contém informações do usuário
        $timeline = Timeline::first();
        $this->assertNotNull($timeline);
        $this->assertNull($timeline->payload['user']);
    }

    public function test_timeline_entry_with_all_status_transitions(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();
        $statuses = [
            PreRegistration::STATUS_WAITING,
            PreRegistration::STATUS_ACCEPTED,
            PreRegistration::STATUS_REJECTED,
            PreRegistration::STATUS_SUMMONED,
            PreRegistration::STATUS_IN_CONFIRMATION,
        ];

        $statusNames = [
            PreRegistration::STATUS_WAITING => 'Aguardando',
            PreRegistration::STATUS_ACCEPTED => 'Aceito',
            PreRegistration::STATUS_REJECTED => 'Rejeitado',
            PreRegistration::STATUS_SUMMONED => 'Convocado',
            PreRegistration::STATUS_IN_CONFIRMATION => 'Em Confirmação',
        ];

        foreach ($statuses as $index => $status) {
            if ($index === 0) {
                continue; // Pula o primeiro status
            }

            $event = new PreRegistrationStatusUpdatedEvent(
                preregistration: $preRegistration,
                before: $statuses[$index - 1],
                after: $status,
                beforeJustification: "Justificativa anterior {$index}",
                afterJustification: "Justificativa nova {$index}"
            );

            // Dispara o evento
            event($event);
        }

        $timelines = Timeline::where('model_id', $preRegistration->id)->get();
        $this->assertCount(count($statuses) - 1, $timelines);

        foreach ($timelines as $index => $timeline) {
            $this->assertEquals($statusNames[$statuses[$index]], $timeline->payload['before']['status']);
            $this->assertEquals($statusNames[$statuses[$index + 1]], $timeline->payload['after']['status']);
        }
    }

    public function test_timeline_entry_with_long_justifications(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();
        $longJustificationBefore = str_repeat('a', 1000); // Justificativa anterior muito longa
        $longJustificationAfter = str_repeat('b', 1000); // Justificativa nova muito longa

        $event = new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_ACCEPTED,
            beforeJustification: $longJustificationBefore,
            afterJustification: $longJustificationAfter
        );

        // Dispara o evento
        event($event);

        // Verifica se foi criada uma entrada na timeline
        $this->assertDatabaseHas('timelines', [
            'model_id' => $preRegistration->id,
            'model_type' => get_class($preRegistration),
            'type' => 'preregistration-status-updated',
        ]);

        // Verifica se o payload contém as justificativas longas
        $timeline = Timeline::first();
        $this->assertNotNull($timeline);
        $this->assertEquals($longJustificationBefore, $timeline->payload['before']['justification']);
        $this->assertEquals($longJustificationAfter, $timeline->payload['after']['justification']);
    }

    public function test_no_timeline_entry_when_status_and_justification_not_changed(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        // Tenta atualizar mantendo o mesmo status e a mesma justificativa
        $event = new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_WAITING,
            beforeJustification: 'Mesma justificativa',
            afterJustification: 'Mesma justificativa'
        );

        // Dispara o evento
        event($event);

        // Verifica se não foi criada nenhuma entrada na timeline
        $this->assertDatabaseMissing('timelines', [
            'model_id' => $preRegistration->id,
            'model_type' => get_class($preRegistration),
            'type' => 'preregistration-status-updated',
        ]);
    }
}
