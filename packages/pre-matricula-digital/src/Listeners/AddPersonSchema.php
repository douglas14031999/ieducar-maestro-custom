<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddPersonSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/person.graphql');
    }
}
