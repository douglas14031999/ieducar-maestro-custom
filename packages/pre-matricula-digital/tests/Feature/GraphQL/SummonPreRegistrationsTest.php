<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;
use Illuminate\Support\Facades\Event;

class SummonPreRegistrationsTest extends GraphQLTestCase
{
    public function test_success(): void
    {
        $preregistration = PreRegistrationFactory::new()->accepted()->create();
        $beforeStatus = $preregistration->status;

        $query = '
            mutation summonPreRegistrations(
                $ids: [ID!]!
                $justification: String
            ) {
                summonPreRegistrations(
                    ids: $ids
                    justification: $justification
                ) {
                    id
                }
            }
        ';

        Event::fake([
            PreRegistrationStatusUpdatedEvent::class,
        ]);

        $this->graphQL($query, [
            'ids' => [$preregistration->getKey()],
            'justification' => 'Justificativa',
        ])->assertJson([
            'data' => [
                'summonPreRegistrations' => [
                    [
                        'id' => $preregistration->getKey(),
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $preregistration->getKey(),
            'status' => PreRegistration::STATUS_SUMMONED,
            'observation' => "Justificativa\n",
        ]);

        Event::assertDispatched(PreRegistrationStatusUpdatedEvent::class, function ($event) use ($preregistration, $beforeStatus) {
            return $event->preregistration->id === $preregistration->id
                && $event->before === $beforeStatus
                && $event->after === PreRegistration::STATUS_SUMMONED;
        });
    }
}
