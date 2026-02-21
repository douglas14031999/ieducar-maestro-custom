<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddTimelineSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/timeline.graphql');
    }
}
