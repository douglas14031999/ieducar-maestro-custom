<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Queries;

use iEducar\Packages\PreMatricula\Models\SearchStudentByName;

class SearchLegacyStudentsByName
{
    public function __invoke($_, array $args): array
    {
        $name = $args['name'];
        $first = $args['first'] ?? 20;

        return SearchStudentByName::query()
            ->whereRaw('f_unaccent(name) ILIKE f_unaccent(?)', [$name . '%'])
            ->limit($first)
            ->get()
            ->toArray();
    }
}
