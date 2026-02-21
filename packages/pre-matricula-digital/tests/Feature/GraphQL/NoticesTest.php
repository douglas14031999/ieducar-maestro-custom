<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\NoticeFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class NoticesTest extends GraphQLTestCase
{
    protected bool $logged = false;

    public function test()
    {
        $notice = NoticeFactory::new()->create();

        $query = '
            query notices {
                notices {
                    data {
                        id
                        text
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'notices' => [
                    'data' => [
                        [
                            'id' => $notice->id,
                            'text' => $notice->text,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_notice()
    {
        $notice = NoticeFactory::new()->create();

        $query = '
            query notice($id: ID!) {
                notice(id: $id) {
                    id
                    text
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $notice->id,
        ])->assertJson([
            'data' => [
                'notice' => [
                    'id' => $notice->id,
                    'text' => $notice->text,
                ],
            ],
        ]);
    }
}
