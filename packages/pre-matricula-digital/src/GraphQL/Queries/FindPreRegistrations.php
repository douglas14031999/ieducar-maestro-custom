<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Queries;

use iEducar\Packages\PreMatricula\Services\Concerns\PreRegistrationsFinder;

abstract class FindPreRegistrations
{
    private $finder;

    public function __construct(PreRegistrationsFinder $finder)
    {
        $this->finder = $finder;
    }

    public function __invoke($_, array $args)
    {
        return $this->finder->find($args);
    }
}
