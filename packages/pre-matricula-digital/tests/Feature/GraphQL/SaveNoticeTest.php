<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\NoticeFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class SaveNoticeTest extends GraphQLTestCase
{
    public function test_create(): void
    {
        $query = '
            mutation save(
                $input: NoticeInput!
            ) {
                notice: saveNotice(
                    input: $input
                ) {
                    text
                }
            }
        ';

        $this->graphQL($query, [
            'input' => [
                'id' => null,
                'text' => 'Aviso teste!',
            ],
        ])->assertJson([
            'data' => [
                'notice' => [
                    'text' => 'Aviso teste!',
                ],
            ],
        ]);
    }

    public function test_update(): void
    {
        $notice = NoticeFactory::new()->create();

        $query = '
            mutation save(
                $input: NoticeInput!
            ) {
                notice: saveNotice(
                    input: $input
                ) {
                    id
                }
            }
        ';

        $this->graphQL($query, [
            'input' => [
                'id' => $notice->getKey(),
                'text' => 'Aviso teste!',
            ],
        ])->assertJson([
            'data' => [
                'notice' => [
                    'id' => $notice->getKey(),
                ],
            ],
        ]);
    }
}
