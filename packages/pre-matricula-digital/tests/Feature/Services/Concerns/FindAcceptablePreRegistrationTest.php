<?php

use iEducar\Packages\PreMatricula\Services\Concerns\FindAcceptablePreRegistration;
use iEducar\Packages\PreMatricula\Tests\TestCase;

uses(TestCase::class);

test('invalid data', function () {
    $service = new FindAcceptablePreRegistration;

    expect($service->find([]))->toBeEmpty();
});
