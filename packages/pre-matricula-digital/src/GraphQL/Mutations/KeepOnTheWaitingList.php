<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Exceptions\PreRegistrationValidationException;
use iEducar\Packages\PreMatricula\Models\PreRegistration;

class KeepOnTheWaitingList
{
    public function __invoke($_, array $args)
    {
        $preregistration = $this->find($args['id']);
        $process = $preregistration->process;

        if ($preregistration->status !== PreRegistration::STATUS_IN_CONFIRMATION) {
            throw PreRegistrationValidationException::isNotInConfirmation();
        }

        $canUpdateGrade = $process->force_suggested_grade || $process->block_incompatible_age_group;

        if ($canUpdateGrade && empty($args['grade'])) {
            throw PreRegistrationValidationException::gradeIsRequired();
        }

        $preregistration->returnToWait();

        if ($canUpdateGrade) {
            $preregistration->updateGrade($args['grade']);
        }

        $preregistration->saveOrFail();

        return true;
    }

    private function find(int $id): PreRegistration
    {
        return PreRegistration::query()->findOrFail($id);
    }
}
