<?php

namespace iEducar\Packages\PreMatricula\Tests\Unit\Events;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\UserFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStudentUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PreRegistrationStudentTimelineTest extends TestCase
{
    public function test_student_updated_without_user(): void
    {
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationStudentUpdatedEvent(
            preregistration: $preRegistration,
            before: ['name' => 'João'],
            after: ['name' => 'João Silva']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-student-updated', $timeline->type);
        $this->assertEquals(['name' => 'João'], $timeline->payload['before']);
        $this->assertEquals(['name' => 'João Silva'], $timeline->payload['after']);
    }

    public function test_student_updated_with_user(): void
    {
        $user = UserFactory::new()->create();
        Auth::login($user);
        $preRegistration = PreRegistrationFactory::new()->create();

        $event = new PreRegistrationStudentUpdatedEvent(
            preregistration: $preRegistration,
            before: ['name' => 'João'],
            after: ['name' => 'João Silva']
        );
        event($event);

        $timeline = Timeline::first();
        $this->assertEquals('preregistration-student-updated', $timeline->type);
        $this->assertEquals(['name' => 'João'], $timeline->payload['before']);
        $this->assertEquals(['name' => 'João Silva'], $timeline->payload['after']);
    }
}
