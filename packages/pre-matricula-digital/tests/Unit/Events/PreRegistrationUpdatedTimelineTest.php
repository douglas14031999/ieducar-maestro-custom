<?php

namespace iEducar\Packages\PreMatricula\Tests\Unit\Events;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\UserFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PreRegistrationUpdatedTimelineTest extends TestCase
{
    public function test_preregistration_updated_without_user(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationUpdatedEvent(
            preregistration: $preRegistration,
            before: ['grade' => '1º Ano'],
            after: ['grade' => '2º Ano']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-updated', $timeline->type);
        $this->assertEquals(['grade' => '1º Ano'], $timeline->payload['before']);
        $this->assertEquals(['grade' => '2º Ano'], $timeline->payload['after']);
    }

    public function test_preregistration_updated_with_user(): void
    {
        $user = UserFactory::new()->create();
        Auth::login($user);
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationUpdatedEvent(
            preregistration: $preRegistration,
            before: ['grade' => '1º Ano'],
            after: ['grade' => '2º Ano']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-updated', $timeline->type);
        $this->assertEquals(['grade' => '1º Ano'], $timeline->payload['before']);
        $this->assertEquals(['grade' => '2º Ano'], $timeline->payload['after']);
    }
}
