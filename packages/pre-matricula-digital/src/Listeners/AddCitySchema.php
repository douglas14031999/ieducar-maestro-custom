<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddCitySchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/city.graphql');
    }
}
