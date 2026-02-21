<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddBaseSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/base.graphql');
    }
}
