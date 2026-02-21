<?php

namespace iEducar\Packages\PreMatricula\Providers;

use iEducar\Packages\PreMatricula\Events\PreRegistrationAddressUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationExternalSystemUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationResponsibleUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusAutoRejectedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStudentUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\PreRegistrationUpdatedEvent;
use iEducar\Packages\PreMatricula\Events\ProcessSavedEvent;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineAddressListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineExternalSystemListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelinePreRegistrationListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineResponsibleListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineStatusAutoRejectedListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineStatusListener;
use iEducar\Packages\PreMatricula\Listeners\CreateTimelineStudentListener;
use iEducar\Packages\PreMatricula\Listeners\MinhaVagaNaCrecheListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class PreMatriculaEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        PreRegistrationAddressUpdatedEvent::class => [
            CreateTimelineAddressListener::class,
        ],
        PreRegistrationStatusUpdatedEvent::class => [
            CreateTimelineStatusListener::class,
        ],
        PreRegistrationResponsibleUpdatedEvent::class => [
            CreateTimelineResponsibleListener::class,
        ],
        PreRegistrationStudentUpdatedEvent::class => [
            CreateTimelineStudentListener::class,
        ],
        PreRegistrationExternalSystemUpdatedEvent::class => [
            CreateTimelineExternalSystemListener::class,
        ],
        PreRegistrationUpdatedEvent::class => [
            CreateTimelinePreRegistrationListener::class,
        ],
        PreRegistrationStatusAutoRejectedEvent::class => [
            CreateTimelineStatusAutoRejectedListener::class,
        ],
        ProcessSavedEvent::class => [
            MinhaVagaNaCrecheListener::class,
        ],
    ];
}
