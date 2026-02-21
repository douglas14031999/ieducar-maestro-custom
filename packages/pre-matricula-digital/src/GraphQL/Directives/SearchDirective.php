<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Directives;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective;

class SearchDirective extends BaseDirective implements ArgBuilderDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'SDL'
"""
Use an input value as a search filter.
"""
directive @search(
  """
  Specify the columns where to search.
  """
  columns: [String]

  """
  Specify type of search.
  """
  type: String
) on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
SDL;
    }

    /**
     * Add any "WHERE" clause to the builder.
     *
     * @param  Builder  $builder
     * @param  string  $value
     * @return Builder
     */
    public function handleBuilder(QueryBuilder|EloquentBuilder|Relation $builder, mixed $value): QueryBuilder|EloquentBuilder|Relation
    {
        return $builder->search(
            $this->directiveArgValue('columns', $this->nodeName()),
            $value,
            $this->directiveArgValue('type', $this->nodeName()),
        );
    }
}
