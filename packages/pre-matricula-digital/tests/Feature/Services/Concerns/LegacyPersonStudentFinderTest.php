<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use iEducar\Packages\PreMatricula\Services\Concerns\LegacyPersonStudentFinder;
use iEducar\Packages\PreMatricula\Tests\TestCase;

uses(TestCase::class);

test('find student by `CPF`', function () {
    $individual = LegacyIndividualFactory::new()->create([
        'cpf' => '12345678900',
    ]);

    LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual,
    ]);

    $finder = new LegacyPersonStudentFinder;

    $person = $finder->find([
        'cpf' => '12345678900',
    ]);

    expect($person->real_name)->toEqual($individual->real_name);
});

test('find student by `RG`', function () {
    $individual = LegacyIndividualFactory::new()->withDocument(rg: '1234567890')->create();

    LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual,
    ]);

    $finder = new LegacyPersonStudentFinder;

    $person = $finder->find([
        'rg' => '1234567890',
    ]);

    expect($person->real_name)->toEqual($individual->real_name);
});

test('find student by `birth certificate`', function () {
    $individual = LegacyIndividualFactory::new()->withDocument(birthCertificate: '12345678901234567890123456789012')->create();

    LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual,
    ]);

    $finder = new LegacyPersonStudentFinder;

    $person = $finder->find([
        'birth_certificate' => '12345678901234567890123456789012',
    ]);

    expect($person->real_name)->toEqual($individual->real_name);
});

test('find student by `name` and `date of birth`', function () {
    $individual = LegacyIndividualFactory::new()->create();

    LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual,
    ]);

    $finder = new LegacyPersonStudentFinder;

    $person = $finder->find([
        'name' => $individual->real_name,
        'date_of_birth' => $individual->data_nasc,
    ]);

    expect($person->real_name)->toEqual($individual->real_name);
});

test('not find student without inform data', function () {
    $finder = new LegacyPersonStudentFinder;

    $person = $finder->find([]);

    expect($person)->toBeNull();
});
