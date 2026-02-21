<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Models\Notice;
use Throwable;

class SaveNotice
{
    /**
     * @return Notice
     *
     * @throws Throwable
     */
    private function saveNotice(?int $id, array $attributes)
    {
        /** @var Notice $notice */
        $notice = $id ? Notice::findOrFail($id) : new Notice;

        $attributes = collect($attributes)->only([
            'text',
        ])->toArray();

        $notice->fill($attributes);
        $notice->saveOrFail();

        return $notice;
    }

    public function __invoke($_, array $args)
    {
        return $this->saveNotice($args['id'], $args);
    }
}
