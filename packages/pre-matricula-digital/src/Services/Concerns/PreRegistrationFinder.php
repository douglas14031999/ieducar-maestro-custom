<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

class PreRegistrationFinder extends Finder
{
    public function __construct($finders = [])
    {
        if (empty($finders)) {
            $this->finders = [
                new FindAcceptablePreRegistration,
            ];
        }
    }
}
