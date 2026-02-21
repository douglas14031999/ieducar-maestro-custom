<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use App\Models\City;
use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Models\PersonHasPlace;
use App\Models\Place;
use Carbon\Carbon;
use iEducar\Packages\PreMatricula\Events\PreRegistrationExternalSystemUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\Person;
use iEducar\Packages\PreMatricula\Models\PersonAddress;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Support\OnlyNumbers;

class UpdateStudentInExternalSystem
{
    use OnlyNumbers;

    private PreRegistration $preregistration;

    private function formatPostalCode(?string $postalCode): string
    {
        if (empty($postalCode)) {
            return '';
        }

        // Se já estiver formatado (contém traço), retorna como está
        if (str_contains($postalCode, '-')) {
            return $postalCode;
        }

        $numbers = $this->onlyNumbers($postalCode);

        if (strlen($numbers) !== 8) {
            return $postalCode;
        }

        return substr($numbers, 0, 5) . '-' . substr($numbers, 5);
    }

    private function formatDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return $date;
        }
    }

    public function __invoke($_, array $args): bool
    {
        /** @var PreRegistration $preregistration */
        $preregistration = PreRegistration::query()->with('student')->findOrFail($args['preregistration']);
        $this->preregistration = $preregistration;

        $externalPersonId = $preregistration->student->external_person_id;

        if (empty($externalPersonId)) {
            return false; // @codeCoverageIgnore
        }

        $person = [];
        $individual = [];
        $document = [];
        $phone = [];

        /** @var PersonAddress $address */
        $address = $preregistration->student->addresses()->first();

        if ($args['name'] ?? false) {
            $person['nome'] = $preregistration->student->name;
        }

        if ($args['cpf'] ?? false) {
            $individual['cpf'] = $this->onlyNumbers($preregistration->student->cpf);
        }

        if ($args['dateOfBirth'] ?? false) {
            $individual['data_nasc'] = $preregistration->student->date_of_birth;
        }

        if ($args['gender'] ?? false) {
            $individual['sexo'] = match ($preregistration->student->gender) {
                Person::GENDER_FEMALE => 'F', // @codeCoverageIgnore
                Person::GENDER_MALE => 'M',
                default => null, // @codeCoverageIgnore
            };
        }

        if ($args['rg'] ?? false) {
            $document['rg'] = $preregistration->student->rg;
        }

        if ($args['birthCertificate'] ?? false) {
            $document['certidao_nascimento'] = $preregistration->student->birth_certificate;
        }

        if ($args['phone'] ?? false) {
            $phone['phone'] = $preregistration->student->phone ?? $preregistration->responsible->phone;
            $phone['mobile'] = $preregistration->student->mobile ?? $preregistration->responsible->mobile;
        }

        if ($person) {
            $this->updatePerson($externalPersonId, $person);
        }

        if ($individual) {
            $this->updateIndividual($externalPersonId, $individual);
        }

        if ($document) {
            $this->updateDocument($externalPersonId, $document);
        }

        if ($phone) {
            $this->updatePhones($externalPersonId, $phone);
        }

        if ($address) {
            $this->updateAddress($externalPersonId, $address);
        }

        return $person || $individual || $document || $phone || $address;
    }

    private function updatePerson(int $id, array $person): void
    {
        $before = LegacyPerson::query()
            ->where('idpes', $id)
            ->first(array_keys($person));

        LegacyPerson::query()->updateOrCreate([
            'idpes' => $id,
        ], $person);

        event(new PreRegistrationExternalSystemUpdatedEvent(
            preregistration: $this->preregistration,
            type: 'name',
            before: ['name' => $before?->nome],
            after: ['name' => $person['nome']]
        ));
    }

    private function updateIndividual(int $id, array $individual): void
    {
        $before = LegacyIndividual::query()
            ->where('idpes', $id)
            ->first(array_keys($individual));

        LegacyIndividual::query()->updateOrCreate([
            'idpes' => $id,
        ], $individual);

        $beforeData = [];
        $afterData = [];

        foreach ($individual as $field => $value) {
            $key = match ($field) {
                'data_nasc' => 'date_of_birth',
                default => $field,
            };

            $beforeData[$key] = match ($field) {
                'data_nasc' => $this->formatDate($before?->$field),
                'cpf' => $this->onlyNumbers($before?->$field),
                default => $before?->$field,
            };

            $afterData[$key] = match ($field) {
                'data_nasc' => $this->formatDate($value),
                'cpf' => $this->onlyNumbers($value),
                default => $value,
            };
        }

        event(new PreRegistrationExternalSystemUpdatedEvent(
            preregistration: $this->preregistration,
            type: 'individual',
            before: $beforeData,
            after: $afterData
        ));
    }

    private function updateDocument(int $id, array $document): void
    {
        $before = LegacyDocument::query()
            ->where('idpes', $id)
            ->first(array_keys($document));

        LegacyDocument::query()->updateOrCreate([
            'idpes' => $id,
        ], $document);

        $beforeData = [];
        $afterData = [];

        foreach ($document as $field => $value) {
            $key = $field === 'certidao_nascimento' ? 'birth_certificate' : $field;
            $beforeData[$key] = $before?->$field;
            $afterData[$key] = $value;
        }

        event(new PreRegistrationExternalSystemUpdatedEvent(
            preregistration: $this->preregistration,
            type: 'documents',
            before: $beforeData,
            after: $afterData
        ));
    }

    private function updatePhones(int $id, array $phones): void
    {
        $beforeData = [];
        $afterData = [];

        if (isset($phones['phone'])) {
            $beforePhone = LegacyPhone::query()
                ->where('idpes', $id)
                ->where('tipo', 1)
                ->first(['ddd', 'fone']);

            LegacyPhone::query()->updateOrCreate([
                'idpes' => $id,
                'tipo' => 1,
            ], [
                'ddd' => $this->getDdd($phones['phone']),
                'fone' => $this->getPhoneNumber($phones['phone']),
            ]);

            $beforeData['phone'] = [
                'ddd' => $beforePhone?->ddd,
                'number' => $beforePhone?->fone,
            ];

            $afterData['phone'] = [
                'ddd' => $this->getDdd($phones['phone']),
                'number' => $this->getPhoneNumber($phones['phone']),
            ];
        }

        if (isset($phones['mobile'])) {
            $beforeMobile = LegacyPhone::query()
                ->where('idpes', $id)
                ->where('tipo', 2)
                ->first(['ddd', 'fone']);

            LegacyPhone::query()->updateOrCreate([
                'idpes' => $id,
                'tipo' => 2,
            ], [
                'ddd' => $this->getDdd($phones['mobile']),
                'fone' => $this->getPhoneNumber($phones['mobile']),
            ]);

            $beforeData['mobile'] = [
                'ddd' => $beforeMobile?->ddd,
                'number' => $beforeMobile?->fone,
            ];

            $afterData['mobile'] = [
                'ddd' => $this->getDdd($phones['mobile']),
                'number' => $this->getPhoneNumber($phones['mobile']),
            ];
        }

        event(new PreRegistrationExternalSystemUpdatedEvent(
            preregistration: $this->preregistration,
            type: 'phones',
            before: $beforeData,
            after: $afterData
        ));
    }

    private function updateAddress(int $id, PersonAddress $address): void
    {
        $city = City::query()->where('name', $address->city)->first();

        if (empty($city)) {
            return; // @codeCoverageIgnore
        }

        $before = PersonHasPlace::query()
            ->where('person_id', $id)
            ->where('type', 1)
            ->first(['place_id'])?->place;

        $place = Place::query()->create([
            'address' => $address->address,
            'number' => $address->number ?: null,
            'complement' => $address->complement,
            'neighborhood' => $address->neighborhood,
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
            'city_id' => $city->getKey(),
            'postal_code' => $this->onlyNumbers($address->postal_code),
        ]);

        PersonHasPlace::query()->updateOrCreate([
            'person_id' => $id,
            'type' => 1,
        ], [
            'place_id' => $place->getKey(),
        ]);

        event(new PreRegistrationExternalSystemUpdatedEvent(
            preregistration: $this->preregistration,
            type: 'address',
            before: $before ? [
                'address' => $before->address,
                'number' => $before->number,
                'complement' => $before->complement,
                'neighborhood' => $before->neighborhood,
                'postal_code' => $this->formatPostalCode($before->postal_code),
                'city' => $before->city?->name,
            ] : [],
            after: [
                'address' => $address->address,
                'number' => $address->number,
                'complement' => $address->complement,
                'neighborhood' => $address->neighborhood,
                'postal_code' => $this->formatPostalCode($address->postal_code),
                'city' => $address->city,
            ]
        ));
    }
}
