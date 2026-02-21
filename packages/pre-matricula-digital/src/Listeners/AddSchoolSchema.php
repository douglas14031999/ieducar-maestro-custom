<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddSchoolSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/school.graphql');
    }
}
