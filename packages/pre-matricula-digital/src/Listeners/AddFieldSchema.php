<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddFieldSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/field.graphql');
    }
}
