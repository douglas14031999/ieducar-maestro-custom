<?php

use iEducar\Packages\PreMatricula\Services\Concerns\PreRegistrationsFinder;
use iEducar\Packages\PreMatricula\Tests\TestCase;

uses(TestCase::class);

test('invalid data', function () {
    $service = new PreRegistrationsFinder;

    expect($service->find([]))->toBeEmpty();
});
