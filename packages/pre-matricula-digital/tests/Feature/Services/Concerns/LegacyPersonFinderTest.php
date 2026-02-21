<?php

use Database\Factories\LegacyIndividualFactory;
use iEducar\Packages\PreMatricula\Services\Concerns\LegacyPersonFinder;
use iEducar\Packages\PreMatricula\Tests\TestCase;

uses(TestCase::class);

test('find person by `CPF`', function () {
    $individual = LegacyIndividualFactory::new()->create([
        'cpf' => '12345678900',
    ]);

    $finder = new LegacyPersonFinder;

    $person = $finder->find([
        'cpf' => '12345678900',
    ]);

    expect($person->real_name)->toEqual($individual->real_name);
});

test('find person by `RG`', function () {
    $individual = LegacyIndividualFactory::new()->withDocument(rg: '1234567890')->create();

    $finder = new LegacyPersonFinder;

    $person = $finder->find([
        'rg' => '1234567890',
    ]);

    expect($person->real_name)->toEqual($individual->real_name);
});

test('find person by `birth certificate`', function () {
    $individual = LegacyIndividualFactory::new()->withDocument(birthCertificate: '12345678901234567890123456789012')->create();

    $finder = new LegacyPersonFinder;

    $person = $finder->find([
        'birth_certificate' => '12345678901234567890123456789012',
    ]);

    expect($person->real_name)->toEqual($individual->real_name);
});

test('find person by `name` and `date of birth`', function () {
    $individual = LegacyIndividualFactory::new()->create();

    $finder = new LegacyPersonFinder;

    $person = $finder->find([
        'name' => $individual->real_name,
        'date_of_birth' => $individual->data_nasc,
    ]);

    expect($person->real_name)->toEqual($individual->real_name);
});

test('not find person without inform data', function () {
    $finder = new LegacyPersonFinder;

    $person = $finder->find([]);

    expect($person)->toBeNull();
});
