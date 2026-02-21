<?php

namespace iEducar\Packages\PreMatricula\Tests;

use iEducar\Packages\PreMatricula\Database\Factories\UserFactory;
use iEducar\Packages\PreMatricula\Providers\PreMatriculaServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Laravel\Sanctum\SanctumServiceProvider;
use Nuwave\Lighthouse\Auth\AuthServiceProvider;
use Nuwave\Lighthouse\Cache\CacheServiceProvider;
use Nuwave\Lighthouse\GlobalId\GlobalIdServiceProvider;
use Nuwave\Lighthouse\LighthouseServiceProvider;
use Nuwave\Lighthouse\OrderBy\OrderByServiceProvider;
use Nuwave\Lighthouse\Pagination\PaginationServiceProvider;
use Nuwave\Lighthouse\Scout\ScoutServiceProvider;
use Nuwave\Lighthouse\SoftDeletes\SoftDeletesServiceProvider;
use Nuwave\Lighthouse\Testing\TestingServiceProvider;
use Nuwave\Lighthouse\Validation\ValidationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected bool $logged = false;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->logged) {
            $this->actingAs(UserFactory::new()->create());
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            PreMatriculaServiceProvider::class,
            LighthouseServiceProvider::class,
            AuthServiceProvider::class,
            CacheServiceProvider::class,
            GlobalIdServiceProvider::class,
            OrderByServiceProvider::class,
            PaginationServiceProvider::class,
            ScoutServiceProvider::class,
            SoftDeletesServiceProvider::class,
            TestingServiceProvider::class,
            ValidationServiceProvider::class,
            SanctumServiceProvider::class,
        ];
    }

    // TODO remover este método quando o sincronismo entre i-Educar e PMD estiver finalizado
    protected function defineDatabaseMigrations()
    {
        if (env('LEGACY_CODE')) {
            RefreshDatabaseState::$migrated = true;
        }
    }
}
