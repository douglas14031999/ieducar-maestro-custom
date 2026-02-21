<?php

namespace iEducar\Packages\PreMatricula\Tests;

use App\User;
use iEducar\Packages\PreMatricula\Providers\PreMatriculaServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\CreatesApplication;
use Tests\TestCase;

abstract class PreMatriculaTestCase extends TestCase
{
    use CreatesApplication;

    /**
     * Indica se o teste deve ser executado utilizando um usuário logado.
     *
     * @see PreMatriculaTestCase::user()
     *
     * @var bool
     */
    protected $logged = false;

    /**
     * Retorna um usuário.
     *
     * @return Authenticatable
     */
    protected function user()
    {
        return User::find(1);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(PreMatriculaServiceProvider::class);

        if ($this->logged) {
            $this->actingAs($this->user());
        }
    }
}
