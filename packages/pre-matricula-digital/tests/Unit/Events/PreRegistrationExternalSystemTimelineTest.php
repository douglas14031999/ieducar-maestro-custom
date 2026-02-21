<?php

namespace iEducar\Packages\PreMatricula\Tests\Unit\Events;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\UserFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationExternalSystemUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PreRegistrationExternalSystemTimelineTest extends TestCase
{
    public function test_external_system_updated_without_user(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationExternalSystemUpdatedEvent(
            preregistration: $preRegistration,
            type: 'address',
            before: ['address' => 'Rua A'],
            after: ['address' => 'Rua B']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-external-system-address-updated', $timeline->type);
    }

    public function test_external_system_updated_with_user(): void
    {
        $user = UserFactory::new()->create();
        Auth::login($user);
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationExternalSystemUpdatedEvent(
            preregistration: $preRegistration,
            type: 'address',
            before: ['address' => 'Rua A'],
            after: ['address' => 'Rua B']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-external-system-address-updated', $timeline->type);
    }
}
