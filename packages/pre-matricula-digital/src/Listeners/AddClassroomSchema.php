<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddClassroomSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/classroom.graphql');
    }
}
