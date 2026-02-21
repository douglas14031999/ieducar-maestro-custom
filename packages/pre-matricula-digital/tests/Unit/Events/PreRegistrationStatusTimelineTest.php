<?php

namespace iEducar\Packages\PreMatricula\Tests\Unit\Events;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\UserFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PreRegistrationStatusTimelineTest extends TestCase
{
    public function test_status_updated_without_user(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_ACCEPTED
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-status-updated', $timeline->type);
        $this->assertEquals(['status' => 'Aguardando'], $timeline->payload['before']);
        $this->assertEquals(['status' => 'Aceito'], $timeline->payload['after']);
    }

    public function test_status_updated_with_user(): void
    {
        $user = UserFactory::new()->create();
        Auth::login($user);
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationStatusUpdatedEvent(
            preregistration: $preRegistration,
            before: PreRegistration::STATUS_WAITING,
            after: PreRegistration::STATUS_ACCEPTED
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-status-updated', $timeline->type);
        $this->assertEquals(['status' => 'Aguardando'], $timeline->payload['before']);
        $this->assertEquals(['status' => 'Aceito'], $timeline->payload['after']);
    }
}
