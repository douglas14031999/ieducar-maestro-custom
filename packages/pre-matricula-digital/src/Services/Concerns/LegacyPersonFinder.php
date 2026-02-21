<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

class LegacyPersonFinder extends Finder
{
    public function __construct($finders = [])
    {
        if (empty($finders)) {
            $this->finders = [
                new FindLegacyPersonByCpf,
                new FindLegacyPersonByRg,
                new FindLegacyPersonByBirthCertificate,
                new FindLegacyPersonByNameAndDateOfBirth,
            ];
        }
    }
}
