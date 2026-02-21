<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use App\Models\City;
use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Models\LegacyStudent;
use App\Models\PersonHasPlace;
use App\Models\Place;
use iEducar\Packages\PreMatricula\Models\Person;
use iEducar\Packages\PreMatricula\Models\PersonAddress;
use iEducar\Packages\PreMatricula\Models\PreRegistration;

trait FindOrCreatePerson
{
    /**
     * @param  string  $number
     * @return int|null
     */
    protected function onlyNumberOrNull($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        return empty($number) ? null : $number;
    }

    private function createAddress(LegacyPerson $person, ?PersonAddress $address)
    {
        if (empty($address)) {
            return;
        }

        $city = City::query()->where('name', $address->city)->first();

        if (empty($city)) {
            return; // @codeCoverageIgnore
        }

        $place = Place::query()->create(
            [
                'address' => $address->address,
                'number' => $address->number ?: null,
                'complement' => $address->complement,
                'neighborhood' => $address->neighborhood,
                'city_id' => $city->getKey(),
                'postal_code' => idFederal2int($address->postal_code),
            ]
        );

        PersonHasPlace::query()->create(
            [
                'person_id' => $person->getKey(),
                'type' => 1,
                'place_id' => $place->getKey(),
            ]
        );
    }

    private function findPerson($data)
    {
        $finder = new LegacyPersonFinder;

        return $finder->find($data->toArray());
    }

    private function findPersonStudent($data)
    {
        $finder = new LegacyPersonStudentFinder;

        return $finder->find($data->toArray());
    }

    /**
     * @codeCoverageIgnore
     */
    private function getRelationType(PreRegistration $preregistration)
    {
        if ($preregistration->relation_type_id == PreRegistration::RELATION_MOTHER) {
            return 'm';
        }

        if ($preregistration->relation_type_id == PreRegistration::RELATION_FATHER) {
            return 'p';
        }

        if ($preregistration->relation_type_id == PreRegistration::RELATION_GUARDIAN) {
            return 'r';
        }

        return null;
    }

    private function getOrCreateResponsiblePerson(PreRegistration $preregistration)
    {
        $person = $this->findPerson(
            $responsible = $preregistration->responsible
        );

        if ($person) {
            return $person; // @codeCoverageIgnore
        }

        $person = LegacyPerson::query()->create([
            'nome' => $responsible->name,
            'email' => $responsible->email,
            'tipo' => 'F',
        ]);

        LegacyIndividual::query()->create([
            'idpes' => $person->getKey(),
            'sexo' => $this->getGender($responsible->gender),
            'data_nasc' => $responsible->date_of_birth,
            'cpf' => $this->onlyNumberOrNull($responsible->cpf),
            'ideciv' => $responsible->marital_status,
            'idmun_nascimento' => $responsible->place_of_birth,
        ]);

        $this->createAddress($person, $preregistration->responsible->addresses->first());
        $this->createPhone($person, $responsible->phone);
        $this->createDocuments($person, $responsible);

        return $person;
    }

    private function createStudentPerson(PreRegistration $preregistration)
    {
        $responsible = $this->getOrCreateResponsiblePerson($preregistration);

        $student = $preregistration->student;

        $person = LegacyPerson::query()->create([
            'nome' => $student->name,
            'email' => $student->email,
            'tipo' => 'F',
        ]);

        LegacyIndividual::query()->create([
            'idpes' => $person->getKey(),
            'sexo' => $this->getGender($student->gender),
            'data_nasc' => $student->date_of_birth,
            'idpes_mae' => $this->getRelationType($preregistration) == 'm' ? $responsible->getKey() : null,
            'idpes_pai' => $this->getRelationType($preregistration) == 'p' ? $responsible->getKey() : null,
            'cpf' => $this->onlyNumberOrNull($student->cpf),
            'ideciv' => $student->marital_status,
            'idmun_nascimento' => $student->place_of_birth,
        ]);

        $this->createAddress($person, $preregistration->student->addresses->first());
        $this->createPhone($person, $student->phone);
        $this->createDocuments($person, $student);

        return $person;
    }

    public function getOrCreatePerson(PreRegistration $preregistration)
    {
        if ($preregistration->external_person_id) {
            return LegacyPerson::query()->findOrFail($preregistration->external_person_id);
        }

        $person = $this->findPersonStudent(
            $student = $preregistration->student
        );

        if ($person) {
            return $person; // @codeCoverageIgnore
        }

        return $this->createStudentPerson($preregistration);
    }

    private function getOrCreateStudent(LegacyPerson $person, PreRegistration $preregistration)
    {
        return LegacyStudent::query()->firstOrCreate([
            'ref_idpes' => $person->getKey(),
        ], [
            'ativo' => 1,
            'data_cadastro' => now(),
            'tipo_responsavel' => $this->getRelationType($preregistration),
        ]);
    }

    private function createPhone(LegacyPerson $person, $phone)
    {
        if (empty($phone)) {
            return;
        }

        [$ddd, $phone] = str_replace(['(', ' ', '-'], '', explode(')', $phone));
        LegacyPhone::create([
            'idpes' => $person->getKey(),
            'tipo' => 1,
            'ddd' => $ddd,
            'fone' => $phone,
        ]);
    }

    private function createDocuments(LegacyPerson $person, Person $preregistrationPerson)
    {
        if (empty($preregistrationPerson->rg) && empty($preregistrationPerson->birth_certificate)) {
            return; // @codeCoverageIgnore
        }

        LegacyDocument::create([
            'idpes' => $person->getKey(),
            'rg' => $preregistrationPerson->rg,
            'certidao_nascimento' => $preregistrationPerson->birth_certificate,
        ]);
    }

    private function getGender($gender)
    {
        if ($gender == Person::GENDER_FEMALE) {
            return 'F'; // @codeCoverageIgnore
        }

        if ($gender == Person::GENDER_MALE) {
            return 'M'; // @codeCoverageIgnore
        }

        return null;
    }
}
