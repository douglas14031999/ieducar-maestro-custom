<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Directives;

use Illuminate\Support\Str;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgTransformerDirective;

class SlugDirective extends BaseDirective implements ArgDirective, ArgTransformerDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'SDL'
"""
Use Laravel slug to transform an argument value.

Useful for slug strings before compare them into the database.
"""
directive @slug on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
SDL;
    }

    /**
     * @param  string  $argumentValue
     */
    public function transform(mixed $argumentValue): mixed
    {
        return Str::slug($argumentValue, ' ');
    }
}
