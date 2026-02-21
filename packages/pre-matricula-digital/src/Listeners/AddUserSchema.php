<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddUserSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/user.graphql');
    }
}
