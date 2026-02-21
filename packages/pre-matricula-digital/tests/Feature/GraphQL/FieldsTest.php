<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class FieldsTest extends GraphQLTestCase
{
    public function test_get_fields(): void
    {
        $fields = Field::query()->take(10)->get()->map(fn (Field $field) => [
            'id' => $field->id,
            'name' => $field->name,
            'internal' => $field->internal,
            'required' => $field->required,
        ]);

        $query = '
            query fields {
                fields {
                    data {
                        id
                        name
                        internal
                        required
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'fields' => [
                    'data' => $fields->toArray(),
                ],
            ],
        ]);
    }

    public function test_field(): void
    {
        $field = FieldFactory::new()->create();

        $query = '
            query field($id: ID!) {
                field(id: $id) {
                    id
                    name
                    internal
                    required
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $field->id,
        ])->assertJson([
            'data' => [
                'field' => [
                    'id' => $field->id,
                    'name' => $field->name,
                    'internal' => $field->internal,
                    'required' => $field->required,
                ],
            ],
        ]);
    }
}
