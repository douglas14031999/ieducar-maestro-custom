<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class SaveProcessFieldsTest extends GraphQLTestCase
{
    public function test(): void
    {
        $process = ProcessFactory::new()->withRequiredFields()->create();

        $query = '
            mutation saveProcessFields(
                $id: ID!
                $fields: [ProcessFieldInput!]!
            ) {
                process: saveProcessFields(
                    id: $id
                    fields: $fields
                ) {
                    id
                }
            }
        ';

        $fields = Field::query()->required()->get()->map(fn (Field $field) => [
            'field' => $field->getKey(),
            'order' => 1,
            'required' => boolval($field->required),
            'weight' => 0,
        ])->toArray();

        $this->graphQL($query, [
            'id' => $process->getKey(),
            'fields' => $fields,
        ])->assertJson([
            'data' => [
                'process' => [
                    'id' => $process->getKey(),
                ],
            ],
        ]);
    }
}
