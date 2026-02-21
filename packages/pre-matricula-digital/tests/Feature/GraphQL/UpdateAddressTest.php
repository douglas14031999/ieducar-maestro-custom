<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PersonAddressFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationAddressUpdatedEvent;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;
use Illuminate\Support\Facades\Event;

class UpdateAddressTest extends GraphQLTestCase
{
    public function test_update_address(): void
    {
        $field = FieldFactory::new()->student()->create();
        $process = ProcessFactory::new()->complete()->withField($field)->create();
        $preregistration = PreRegistrationFactory::new()->create([
            'process_id' => $process->getKey(),
        ]);

        $address = PersonAddressFactory::new()->create([
            'person_id' => $preregistration->responsible_id,
        ]);

        $this->assertDatabaseHas('person_addresses', [
            'id' => $address->getKey(),
            'address' => $address->address,
            'city' => $address->city,
            'complement' => $address->complement,
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
            'neighborhood' => $address->neighborhood,
            'number' => $address->number,
            'postal_code' => $address->postal_code,
        ]);

        $addressMake = PersonAddressFactory::new()->make([
            'person_id' => $preregistration->responsible_id,
        ]);

        $query = '
            mutation updateAddress(
                $protocol: String!
                $address: AddressInput!
            ) {
                updateAddress(
                    protocol: $protocol
                    address: $address
                ) {
                    id
                    protocol
                }
            }
        ';

        Event::fake([
            PreRegistrationAddressUpdatedEvent::class,
        ]);

        $this->graphQL($query, [
            'protocol' => $preregistration->protocol,
            'address' => [
                'address' => $addressMake->address,
                'city' => $addressMake->city,
                'complement' => $addressMake->complement,
                'lat' => $addressMake->latitude,
                'lng' => $addressMake->longitude,
                'manualChangeLocation' => true,
                'neighborhood' => $addressMake->neighborhood,
                'number' => $addressMake->number,
                'postalCode' => $addressMake->postal_code,
            ],
        ])->assertJson([
            'data' => [
                'updateAddress' => [
                    'id' => $preregistration->getKey(),
                    'protocol' => $preregistration->protocol,
                ],
            ],
        ]);

        $this->assertDatabaseHas('person_addresses', [
            'id' => $address->getKey(),
            'address' => $addressMake->address,
            'city' => $addressMake->city,
            'complement' => $addressMake->complement,
            'latitude' => $addressMake->latitude,
            'longitude' => $addressMake->longitude,
            'neighborhood' => $addressMake->neighborhood,
            'number' => $addressMake->number,
            'postal_code' => $addressMake->postal_code,
        ]);

        Event::assertDispatched(PreRegistrationAddressUpdatedEvent::class, function ($event) use ($preregistration, $address, $addressMake) {
            return $event->preregistration->id === $preregistration->id
                && $event->before['address'] === $address->address
                && $event->after['address'] === $addressMake->address;
        });
    }
}
