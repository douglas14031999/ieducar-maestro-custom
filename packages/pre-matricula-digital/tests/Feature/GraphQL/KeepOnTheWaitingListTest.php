<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class KeepOnTheWaitingListTest extends GraphQLTestCase
{
    public function test_success(): void
    {
        $preregistration = PreRegistrationFactory::new()->inConfirmation()->create();

        $preregistration->process->update([
            'block_incompatible_age_group' => true,
        ]);

        $query = '
            mutation keepOnTheWaitingList(
                $id: ID!
                $grade: ID!
            ) {
                keepOnTheWaitingList(
                    id: $id
                    grade: $grade
                )
            }
        ';

        $this->graphQL($query, [
            'id' => $preregistration->getKey(),
            'grade' => $preregistration->grade_id,
        ])->assertJson([
            'data' => [
                'keepOnTheWaitingList' => true,
            ],
        ]);
    }

    public function test_error(): void
    {
        $preregistration = PreRegistrationFactory::new()->create();

        $query = '
            mutation keepOnTheWaitingList(
                $id: ID!
            ) {
                keepOnTheWaitingList(
                    id: $id
                )
            }
        ';

        $this->graphQL($query, [
            'id' => $preregistration->getKey(),
        ])->assertGraphQLValidationError('message', 'A inscrição não está em confirmação');
    }

    public function test_grade_need_tobe_updated(): void
    {
        $preregistration = PreRegistrationFactory::new()->inConfirmation()->create();

        $preregistration->process->update([
            'force_suggested_grade' => true,
        ]);

        $query = '
            mutation keepOnTheWaitingList(
                $id: ID!
            ) {
                keepOnTheWaitingList(
                    id: $id
                )
            }
        ';

        $this->graphQL($query, [
            'id' => $preregistration->getKey(),
        ])->assertGraphQLValidationError('message', 'A série é obrigatória');
    }
}
