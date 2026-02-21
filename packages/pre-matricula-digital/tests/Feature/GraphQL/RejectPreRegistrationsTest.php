<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;
use Illuminate\Support\Facades\Event;

class RejectPreRegistrationsTest extends GraphQLTestCase
{
    public function test_success(): void
    {
        $preregistration = PreRegistrationFactory::new()->create();
        $beforeStatus = $preregistration->status;

        $query = '
            mutation rejectPreRegistrations(
                $ids: [ID!]!
                $justification: String
            ) {
                rejectPreRegistrations(
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
            'justification' => 'Rejeitado em lote',
        ])->assertJson([
            'data' => [
                'rejectPreRegistrations' => [
                    [
                        'id' => $preregistration->getKey(),
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $preregistration->getKey(),
            'observation' => "Rejeitado em lote\n",
        ]);

        Event::assertDispatched(PreRegistrationStatusUpdatedEvent::class, function ($event) use ($preregistration, $beforeStatus) {
            return $event->preregistration->id === $preregistration->id
                && $event->before === $beforeStatus
                && $event->after === PreRegistration::STATUS_REJECTED;
        });
    }
}
