<?php

namespace iEducar\Packages\PreMatricula\Listeners;

class AddNoticeSchema
{
    public function handle(): string
    {
        return file_get_contents(__DIR__ . '/../../graphql/notice.graphql');
    }
}
