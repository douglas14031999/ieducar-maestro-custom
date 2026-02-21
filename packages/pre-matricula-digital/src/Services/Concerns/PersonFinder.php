<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

class PersonFinder extends Finder
{
    public function __construct($finders = [])
    {
        if (empty($finders)) {
            $this->finders = [
                new FindPersonByCpf,
                new FindPersonByRg,
                new FindPersonByNameAndDateOfBirth,
            ];
        }
    }
}
