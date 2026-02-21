<?php

namespace iEducar\Packages\PreMatricula\Tests\Unit\Events;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\UserFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationAddressUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PreRegistrationAddressTimelineTest extends TestCase
{
    public function test_address_updated_without_user(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationAddressUpdatedEvent(
            preregistration: $preRegistration,
            before: ['address' => 'Rua A'],
            after: ['address' => 'Rua B']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('student-responsible-address-updated', $timeline->type);
        $this->assertEquals(['address' => 'Rua A'], $timeline->payload['before']);
        $this->assertEquals(['address' => 'Rua B'], $timeline->payload['after']);
    }

    public function test_address_updated_with_user(): void
    {
        $user = UserFactory::new()->create();
        Auth::login($user);
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationAddressUpdatedEvent(
            preregistration: $preRegistration,
            before: ['address' => 'Rua A'],
            after: ['address' => 'Rua B']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('student-responsible-address-updated', $timeline->type);
        $this->assertEquals(['address' => 'Rua A'], $timeline->payload['before']);
        $this->assertEquals(['address' => 'Rua B'], $timeline->payload['after']);
    }
}
