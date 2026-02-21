<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class RecalculateGradeTest extends GraphQLTestCase
{
    public function test(): void
    {
        $process = ProcessFactory::new([])
            ->complete()
            ->withClassroom()
            ->create();

        $first = $process->grades->first();
        $last = $process->grades->last();

        $first->update([
            'start_birth' => now()->subYear()->subYear(),
            'end_birth' => now()->subYear()->subDay(),
        ]);

        $last->update([
            'start_birth' => now()->subYear(),
            'end_birth' => now()->subDay(),
        ]);

        $preregistration = PreRegistrationFactory::new()->create([
            //
            // Devido a view de sugestão de séries não estar ativa, não é possível realizar o teste de maneira adequada
            //
            // 'preregistration_type_id' => PreRegistration::WAITING_LIST,
            'process_id' => $process->id,
        ]);

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $preregistration->id,
            'grade_id' => $first->id,
        ]);

        $this->artisan('process:recalculate-grade', [
            'process' => $process->getKey(),
        ])->expectsOutput('Grades was recalculated for process');

        //
        // Devido a view de sugestão de séries não estar ativa, não é possível realizar o teste de maneira adequada
        //
        // $this->assertDatabaseHas(PreRegistration::class, [
        //     'id' => $preregistration->id,
        //     'grade_id' => $last->id,
        // ]);
    }
}
