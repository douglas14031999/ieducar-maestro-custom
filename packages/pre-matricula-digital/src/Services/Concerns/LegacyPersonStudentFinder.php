<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

class LegacyPersonStudentFinder extends Finder
{
    public function __construct($finders = [])
    {
        if (empty($finders)) {
            $this->finders = [
                new FindLegacyPersonStudentByCpf,
                new FindLegacyPersonStudentByRg,
                new FindLegacyPersonStudentByBirthCertificate,
                new FindLegacyPersonStudentByNameAndDateOfBirth,
            ];
        }
    }
}
