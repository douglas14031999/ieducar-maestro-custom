<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Queries;

use iEducar\Packages\PreMatricula\Models\School;

class GetGroupedVacancies
{
    /**
     * @param  int  $school
     * @return School
     */
    private function findSchool($school)
    {
        return School::query()->findOrFail($school);
    }

    public function __invoke($_, array $args)
    {
        return $this->findSchool($args['school'])->getGroupedVacancies(
            $args['schoolYear'],
            $args['grades'],
            $args['periods'],
        );
    }
}
