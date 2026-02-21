<?php

namespace iEducar\Packages\PreMatricula\Providers;

use iEducar\Packages\PreMatricula\Listeners\AddBaseSchema;
use iEducar\Packages\PreMatricula\Listeners\AddCitySchema;
use iEducar\Packages\PreMatricula\Listeners\AddClassroomSchema;
use iEducar\Packages\PreMatricula\Listeners\AddFieldSchema;
use iEducar\Packages\PreMatricula\Listeners\AddNoticeSchema;
use iEducar\Packages\PreMatricula\Listeners\AddPersonSchema;
use iEducar\Packages\PreMatricula\Listeners\AddPreregistrationSchema;
use iEducar\Packages\PreMatricula\Listeners\AddProcessSchema;
use iEducar\Packages\PreMatricula\Listeners\AddSchoolSchema;
use iEducar\Packages\PreMatricula\Listeners\AddTimelineSchema;
use iEducar\Packages\PreMatricula\Listeners\AddUserSchema;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Nuwave\Lighthouse\Events\BuildSchemaString;
use Nuwave\Lighthouse\Schema\Source\SchemaSourceProvider;

class GraphQLServiceProvider extends EventServiceProvider
{
    protected $listen = [
        BuildSchemaString::class => [
            AddBaseSchema::class,
            AddCitySchema::class,
            AddClassroomSchema::class,
            AddFieldSchema::class,
            AddNoticeSchema::class,
            AddPersonSchema::class,
            AddPreregistrationSchema::class,
            AddProcessSchema::class,
            AddSchoolSchema::class,
            AddUserSchema::class,
            AddTimelineSchema::class,
        ],
    ];

    public function register(): void
    {
        parent::register();

        $this->app->booting(function () {
            $this->app->singleton(SchemaSourceProvider::class, static function () {
                return new class implements SchemaSourceProvider
                {
                    public function getSchemaString(): string
                    {
                        return '';
                    }
                };
            });

            config([
                'lighthouse.route.middleware' => ['web'] + config('lighthouse.route.middleware', []),
                'lighthouse.guards' => ['web', 'sanctum'],
                'lighthouse.namespaces' => [
                    'models' => ['iEducar\\Packages\\PreMatricula\\Models'],
                    'queries' => 'iEducar\\Packages\\PreMatricula\\GraphQL\\Queries',
                    'mutations' => 'iEducar\\Packages\\PreMatricula\\GraphQL\\Mutations',
                    'subscriptions' => 'iEducar\\Packages\\PreMatricula\\GraphQL\\Subscriptions',
                    'interfaces' => 'iEducar\\Packages\\PreMatricula\\GraphQL\\Interfaces',
                    'unions' => 'iEducar\\Packages\\PreMatricula\\GraphQL\\Unions',
                    'scalars' => 'iEducar\\Packages\\PreMatricula\\GraphQL\\Scalars',
                    'directives' => ['iEducar\\Packages\\PreMatricula\\GraphQL\\Directives'],
                    'validators' => ['iEducar\\Packages\\PreMatricula\\GraphQL\\Validators'],
                ],
                'lighthouse.pagination.default_count' => 10,
                'lighthouse.force_fill' => true,
            ]);
        });
    }
}
