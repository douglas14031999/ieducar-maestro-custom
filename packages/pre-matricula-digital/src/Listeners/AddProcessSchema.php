<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddProcessSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/process.graphql');
    }
}
