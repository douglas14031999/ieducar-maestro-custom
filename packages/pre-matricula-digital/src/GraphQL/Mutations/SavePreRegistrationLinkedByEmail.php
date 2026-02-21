<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Models\PreRegistrationLinkedByEmail;

class SavePreRegistrationLinkedByEmail
{
    public function __invoke($_, array $args)
    {
        return PreRegistrationLinkedByEmail::query()->updateOrCreate($args);
    }
}
