<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PersonFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Exceptions\PreRegistrationValidationException;
use iEducar\Packages\PreMatricula\Models\Classroom;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Services\EnrollmentService;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;
use Illuminate\Support\Facades\Event;

class AcceptPreRegistrationsTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    private function mockEnrollmentService($withError = false): void
    {
        $service = $this->createMock(EnrollmentService::class);

        $expect = $service->expects($this->once())->method('enroll');

        if ($withError) {
            $expect->willThrowException(
                new PreRegistrationValidationException('AcceptPreRegistrationsTest')
            );
        }

        $this->app->bind(EnrollmentService::class, fn () => $service);
    }

    public function test_success(): void
    {
        $this->mockEnrollmentService();

        $preregistration = PreRegistrationFactory::new()->create();
        $classroom = Classroom::query()->first();
        $beforeStatus = $preregistration->status;

        $query = '
            mutation acceptPreRegistrations($ids: [ID!]!, $classroom: ID!) {
                acceptPreRegistrations(ids: $ids, classroom: $classroom) {
                    id
                }
            }
        ';

        Event::fake([
            PreRegistrationStatusUpdatedEvent::class,
        ]);

        $this->graphQL($query, [
            'ids' => [$preregistration->getKey()],
            'classroom' => $classroom->getKey(),
        ])->assertJson([
            'data' => [
                'acceptPreRegistrations' => [
                    [
                        'id' => $preregistration->getKey(),
                    ],
                ],
            ],
        ]);

        Event::assertDispatched(PreRegistrationStatusUpdatedEvent::class, function ($event) use ($preregistration, $beforeStatus) {
            return $event->preregistration->id === $preregistration->id
                && $event->before === $beforeStatus
                && $event->after === PreRegistration::STATUS_ACCEPTED;
        });
    }

    public function test_error(): void
    {
        $this->mockEnrollmentService(withError: true);

        $preregistration = PreRegistrationFactory::new()->create();
        $classroom = Classroom::query()->first();

        $query = '
            mutation acceptPreRegistrations($ids: [ID!]!, $classroom: ID!) {
                acceptPreRegistrations(ids: $ids, classroom: $classroom) {
                    id
                }
            }
        ';

        $this->graphQL($query, [
            'ids' => [$preregistration->getKey()],
            'classroom' => $classroom->getKey(),
        ])->assertGraphQLErrorMessage('Ocorreu um erro ao finalizar a inscrição');
    }

    public function test_reject_all_in_same_process(): void
    {
        $this->mockEnrollmentService();
        $this->createOpenedProcess();

        $this->process->update([
            'reject_type_id' => Process::REJECT_SAME_PROCESS,
        ]);

        $classroom = Classroom::query()->first();

        $willBeRejected = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
            'student_id' => PersonFactory::new()->create([
                'cpf' => '012.345.678-90',
            ]),
        ]);

        $accepted = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
            'student_id' => PersonFactory::new()->create([
                'cpf' => '012.345.678-90',
            ]),
        ]);

        // Adiciona uma inscrição para o mesmo CPF em outro processo
        PreRegistrationFactory::new()->create([
            'student_id' => PersonFactory::new()->create([
                'cpf' => '012.345.678-90',
            ]),
        ]);

        $query = '
            mutation acceptPreRegistrations($ids: [ID!]!, $classroom: ID!) {
                acceptPreRegistrations(ids: $ids, classroom: $classroom) {
                    id
                }
            }
        ';

        $this->graphQL($query, [
            'ids' => [$accepted->getKey()],
            'classroom' => $classroom->getKey(),
        ])->assertJson([
            'data' => [
                'acceptPreRegistrations' => [
                    [
                        'id' => $accepted->getKey(),
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $willBeRejected->getKey(),
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => "Indeferido devido ao protocolo #$accepted->procotol ser deferido.\n",
        ]);
        $this->assertDatabaseHas('preregistrations', [
            'status' => PreRegistration::STATUS_WAITING,
        ]);
        $this->assertDatabaseHas('preregistrations', [
            'status' => PreRegistration::STATUS_ACCEPTED,
        ]);
    }

    public function test_reject_all_in_same_year(): void
    {
        $this->mockEnrollmentService();
        $this->createOpenedProcess();

        $this->process->update([
            'reject_type_id' => Process::REJECT_SAME_YEAR,
        ]);

        $classroom = Classroom::query()->first();

        $willBeRejected = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
            'student_id' => PersonFactory::new()->create([
                'cpf' => '012.345.678-90',
            ]),
        ]);

        $accepted = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
            'student_id' => PersonFactory::new()->create([
                'cpf' => '012.345.678-90',
            ]),
        ]);

        // Adiciona uma inscrição para o mesmo CPF em outro processo que deverá ser indeferida
        PreRegistrationFactory::new()->create([
            'student_id' => PersonFactory::new()->create([
                'cpf' => '012.345.678-90',
            ]),
        ]);

        $query = '
            mutation acceptPreRegistrations($ids: [ID!]!, $classroom: ID!) {
                acceptPreRegistrations(ids: $ids, classroom: $classroom) {
                    id
                }
            }
        ';

        $this->graphQL($query, [
            'ids' => [$accepted->getKey()],
            'classroom' => $classroom->getKey(),
        ])->assertJson([
            'data' => [
                'acceptPreRegistrations' => [
                    [
                        'id' => $accepted->getKey(),
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $willBeRejected->getKey(),
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => "Indeferido devido ao protocolo #$accepted->procotol ser deferido.\n",
        ]);
        $this->assertDatabaseMissing('preregistrations', [
            'status' => PreRegistration::STATUS_WAITING,
        ]);
        $this->assertDatabaseHas('preregistrations', [
            'status' => PreRegistration::STATUS_ACCEPTED,
        ]);
    }

    public function test_avoid_reject_other_cpf(): void
    {
        $this->mockEnrollmentService();
        $this->createOpenedProcess();

        $this->process->update([
            'reject_type_id' => Process::REJECT_SAME_PROCESS,
        ]);

        $classroom = Classroom::query()->first();

        $willBeRejected = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
        ]);

        $accepted = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
        ]);

        $query = '
            mutation acceptPreRegistrations($ids: [ID!]!, $classroom: ID!) {
                acceptPreRegistrations(ids: $ids, classroom: $classroom) {
                    id
                }
            }
        ';

        $this->graphQL($query, [
            'ids' => [$accepted->getKey()],
            'classroom' => $classroom->getKey(),
        ])->assertJson([
            'data' => [
                'acceptPreRegistrations' => [
                    [
                        'id' => $accepted->getKey(),
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'status' => PreRegistration::STATUS_WAITING,
        ]);
        $this->assertDatabaseHas('preregistrations', [
            'status' => PreRegistration::STATUS_ACCEPTED,
        ]);
    }
}
