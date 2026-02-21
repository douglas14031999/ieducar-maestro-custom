<?php

namespace iEducar\Packages\PreMatricula\Tests;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;

abstract class GraphQLTestCase extends TestCase
{
    use MakesGraphQLRequests;

    protected bool $logged = true;
}
