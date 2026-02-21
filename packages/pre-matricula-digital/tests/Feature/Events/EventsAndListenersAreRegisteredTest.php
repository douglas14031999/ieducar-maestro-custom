<?php

use iEducar\Packages\PreMatricula\Events\PreRegistrationAddressUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationExternalSystemUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationResponsibleUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusAutoRejectedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStudentUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationUpdatedEvent;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineAddressListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineExternalSystemListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelinePreRegistrationListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineResponsibleListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineStatusAutoRejectedListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineStatusListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineStudentListener;
use iEducar\Packages\PreMatricula\Tests\TestCase;
use Illuminate\Support\Facades\Event;

uses(TestCase::class);

test('events and listeners are registered', function () {
    Event::fake();

    Event::assertListening(
        PreRegistrationAddressUpdatedEvent::class,
        CreateTimelineAddressListener::class
    );

    Event::assertListening(
        PreRegistrationStatusUpdatedEvent::class,
        CreateTimelineStatusListener::class
    );

    Event::assertListening(
        PreRegistrationResponsibleUpdatedEvent::class,
        CreateTimelineResponsibleListener::class
    );

    Event::assertListening(
        PreRegistrationStudentUpdatedEvent::class,
        CreateTimelineStudentListener::class
    );

    Event::assertListening(
        PreRegistrationExternalSystemUpdatedEvent::class,
        CreateTimelineExternalSystemListener::class
    );

    Event::assertListening(
        PreRegistrationUpdatedEvent::class,
        CreateTimelinePreRegistrationListener::class
    );

    Event::assertListening(
        PreRegistrationStatusAutoRejectedEvent::class,
        CreateTimelineStatusAutoRejectedListener::class
    );
});
