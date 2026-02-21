<?php

namespace iEducar\Packages\PreMatricula\Tests;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FactoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test(): void
    {
        PreRegistrationFactory::new()->create();

        $this->assertDatabaseCount('fields', 17);
        $this->assertDatabaseCount('classrooms', 1);
        $this->assertDatabaseCount('schools', 1);
        $this->assertDatabaseCount('grades', 1);
        $this->assertDatabaseCount('courses', 1);
        $this->assertDatabaseCount('periods', 1);
        $this->assertDatabaseCount('school_years', 1);
        $this->assertDatabaseCount('processes', 1);
        $this->assertDatabaseCount('process_grade', 1);
        $this->assertDatabaseCount('process_period', 1);
        $this->assertDatabaseCount('process_stages', 1);
        $this->assertDatabaseCount('process_fields', 6);
        $this->assertDatabaseCount('people', 2);
        $this->assertDatabaseCount('preregistrations', 1);
    }
}
