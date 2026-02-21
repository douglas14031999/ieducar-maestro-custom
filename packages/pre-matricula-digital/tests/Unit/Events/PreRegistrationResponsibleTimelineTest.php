<?php

namespace iEducar\Packages\PreMatricula\Tests\Unit\Events;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\UserFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationResponsibleUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PreRegistrationResponsibleTimelineTest extends TestCase
{
    public function test_responsible_updated_without_user(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationResponsibleUpdatedEvent(
            preregistration: $preRegistration,
            before: ['name' => 'Maria'],
            after: ['name' => 'Maria Silva']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-student-responsible-updated', $timeline->type);
        $this->assertEquals(['name' => 'Maria'], $timeline->payload['before']);
        $this->assertEquals(['name' => 'Maria Silva'], $timeline->payload['after']);
    }

    public function test_responsible_updated_with_user(): void
    {
        $user = UserFactory::new()->create();
        Auth::login($user);
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationResponsibleUpdatedEvent(
            preregistration: $preRegistration,
            before: ['name' => 'Maria'],
            after: ['name' => 'Maria Silva']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-student-responsible-updated', $timeline->type);
        $this->assertEquals(['name' => 'Maria'], $timeline->payload['before']);
        $this->assertEquals(['name' => 'Maria Silva'], $timeline->payload['after']);
    }
}
