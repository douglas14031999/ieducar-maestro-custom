<?php

use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Models\PersonHasPlace;
use App\Models\Place;
use Database\Factories\LegacyIndividualFactory;
use iEducar\Packages\PreMatricula\Models\Person;
use iEducar\Packages\PreMatricula\Support\OnlyNumbers;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

uses(GraphQLTestCase::class);
uses(CreateSimpleProcess::class);
uses(OnlyNumbers::class);

test('update student data`', function () {
    $individual = LegacyIndividualFactory::new()
        ->withDocument(
            rg: '11111111',
            birthCertificate: '11111111111111111111111111111111',
        )
        ->withName('Portabilindo Filho')
        ->create([
            'cpf' => 98765432100,
        ]);

    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addAddressToStudent();
    $this->addWaitingListPeriod();

    $this->student->external_person_id = $individual->getKey();
    $this->student->gender = Person::GENDER_MALE;
    $this->student->phone = '(11) 99999-8888';
    $this->student->mobile = '(22) 98888-7777';
    $this->student->save();

    $mutation = '
        mutation updateStudentInExternalSystem(
            $preregistration: ID!
            $cpf: Boolean
            $rg: Boolean
            $birthCertificate: Boolean
            $name: Boolean
            $dateOfBirth: Boolean
            $gender: Boolean
            $phone: Boolean
            $mobile: Boolean
            $address: Boolean
        ) {
            updateStudentInExternalSystem(
                preregistration: $preregistration
                cpf: $cpf
                rg: $rg
                birthCertificate: $birthCertificate
                name: $name
                dateOfBirth: $dateOfBirth
                gender: $gender
                phone: $phone
                mobile: $mobile
                address: $address
            )
        }
    ';

    $variables = [
        'preregistration' => $this->preregistration->getKey(),
        'cpf' => true,
        'rg' => true,
        'birthCertificate' => true,
        'name' => true,
        'dateOfBirth' => true,
        'gender' => true,
        'phone' => true,
        'address' => true,
    ];

    $this->graphQL($mutation, $variables)->assertJson([
        'data' => [
            'updateStudentInExternalSystem' => true,
        ],
    ]);

    $this->assertDatabaseHas(LegacyPerson::class, [
        'idpes' => $individual->getKey(),
        'nome' => $this->student->name,
    ]);

    $this->assertDatabaseHas(LegacyIndividual::class, [
        'idpes' => $individual->getKey(),
        'cpf' => $this->onlyNumbers($this->student->cpf),
        'data_nasc' => $this->student->date_of_birth,
        'sexo' => match ($this->student->gender) {
            Person::GENDER_FEMALE => 'F',
            Person::GENDER_MALE => 'M',
            default => null,
        },
    ]);

    $this->assertDatabaseHas(LegacyDocument::class, [
        'idpes' => $individual->getKey(),
        'rg' => $this->student->rg,
        'certidao_nascimento' => $this->student->birth_certificate,
    ]);

    $this->assertDatabaseHas(LegacyPhone::class, [
        'idpes' => $individual->getKey(),
        'ddd' => $this->getDdd($this->student->phone),
        'fone' => $this->getPhoneNumber($this->student->phone),
    ]);

    $this->assertDatabaseHas(LegacyPhone::class, [
        'idpes' => $individual->getKey(),
        'ddd' => $this->getDdd($this->student->mobile),
        'fone' => $this->getPhoneNumber($this->student->mobile),
    ]);

    $address = $this->student->addresses()->first();

    $place = Place::query()->where([
        'address' => $address->address,
        'number' => $address->number,
        'complement' => $address->complement,
        'neighborhood' => $address->neighborhood,
        'postal_code' => $this->onlyNumbers($address->postal_code),
    ])->first();

    $this->assertDatabaseHas(PersonHasPlace::class, [
        'person_id' => $individual->getKey(),
        'place_id' => $place->getKey(),
        'type' => 1,
    ]);
});
