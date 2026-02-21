<?php

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Services\Concerns\PersonFinder;
use iEducar\Packages\PreMatricula\Tests\TestCase;

uses(TestCase::class);

test('find person by `CPF`', function () {
    $preregistration = PreRegistrationFactory::new()->create();

    $preregistration->student->update([
        'cpf' => '123.456.789-00',
    ]);

    $finder = new PersonFinder;

    $person = $finder->find([
        'cpf' => '123.456.789-00',
        'school_year_id' => $preregistration->process->school_year_id,
    ]);

    expect($person->name)->toEqual($preregistration->student->name);
});

test('find person by `RG`', function () {
    $preregistration = PreRegistrationFactory::new()->create();

    $preregistration->student->update([
        'rg' => '1234567890',
    ]);

    $finder = new PersonFinder;

    $person = $finder->find([
        'rg' => '1234567890',
        'school_year_id' => $preregistration->process->school_year_id,
    ]);

    expect($person->name)->toEqual($preregistration->student->name);
});

test('find person by `name` and `date of birth`', function () {
    $preregistration = PreRegistrationFactory::new()->create();

    $finder = new PersonFinder;

    $person = $finder->find([
        'name' => $preregistration->student->name,
        'date_of_birth' => $preregistration->student->date_of_birth,
        'school_year_id' => $preregistration->process->school_year_id,
    ]);

    expect($person->name)->toEqual($preregistration->student->name);
});

test('not find person without inform data', function () {
    $finder = new PersonFinder;

    $person = $finder->find([]);

    expect($person)->toBeNull();
});
