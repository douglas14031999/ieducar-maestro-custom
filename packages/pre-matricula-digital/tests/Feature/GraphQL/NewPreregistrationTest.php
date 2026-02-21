<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessGrouperFactory;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class NewPreregistrationTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    protected function prepareTestAndGetMutation(): string
    {
        $this->createOpenedProcess();
        $this->addWaitingListPeriod();

        return $this->getMutation();
    }

    public function test_new_preregistration(): void
    {
        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariables();

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_new_waiting_list_after_one_preregistration(): void
    {
        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariables();

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);

        $variables = $this->getVariables();

        $variables['input']['type'] = 'WAITING_LIST';

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_new_preregistration_after_a_previous_one_has_been_created(): void
    {
        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariables();

        $this->process->update([
            'waiting_list_limit' => 1,
        ]);

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);

        $variables['input']['type'] = 'REGISTRATION';

        $this->graphQL($query, $variables)
            ->assertGraphQLErrorMessage('Ocorreu um erro ao finalizar a inscrição')
            ->assertGraphQLValidationError('message', 'Já existe uma inscrição para este(a) aluno(a)');

        $variables['input']['type'] = 'REGISTRATION_RENEWAL';

        $this->graphQL($query, $variables)
            ->assertGraphQLErrorMessage('Ocorreu um erro ao finalizar a inscrição')
            ->assertGraphQLValidationError('message', 'Já existe uma inscrição para este(a) aluno(a)');

        $variables['input']['type'] = 'WAITING_LIST';

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);

        $this->graphQL($query, $variables)
            ->assertGraphQLErrorMessage('Ocorreu um erro ao finalizar a inscrição')
            ->assertGraphQLValidationError('message', 'O limite de inscrição em lista de espera já foi atingido');
    }

    public function test_new_preregistration_without_vacancy(): void
    {
        $this->listenForNewPreregistration();

        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariables();

        // A primeira requisição irá preencher a única vaga disponível

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);

        // Muda os dados do aluno e tenta efetuar uma segunda inscrição, a qual não será possível por não haver mais
        // vagas disponíveis no ano/escola/série/turno

        $variables['input']['student'] = [
            [
                'field' => Field::STUDENT_NAME,
                'value' => 'Aluno Novinho Portabilis',
            ],
            [
                'field' => Field::STUDENT_DATE_OF_BIRTH,
                'value' => '2020-01-01',
            ],
        ];

        $this->graphQL($query, $variables)
            ->assertGraphQLErrorMessage('Ocorreu um erro ao finalizar a inscrição')
            ->assertGraphQLValidationError('message', 'Não há vagas para a série e turno selecionados na escola');
    }

    public function test_new_preregistration_without_school(): void
    {
        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariables();

        $variables['input']['school'] = null;

        $this->graphQL($query, $variables)
            ->assertGraphQLValidationError('message', 'Nenhuma pré-matrícula realizada');
    }

    public function test_new_preregistration_with_custom_field(): void
    {
        $customFieldValue = 'Campo Customizado';

        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariables();

        $field = FieldFactory::new()->student()->create();

        $variables['input']['student'][] = [
            'field' => 'field_' . $field->getKey(),
            'value' => $customFieldValue,
        ];

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('preregistration_fields', [
            'field_id' => $field->getKey(),
            'value' => $customFieldValue,
        ]);
    }

    public function test_new_preregistration_in_another_process(): void
    {
        config([
            'prematricula.only_one_preregistration_by_student' => true,
        ]);

        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariables();

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);

        $process = ProcessFactory::new()->complete()->create([
            'school_year_id' => $this->year->getKey(),
        ]);

        $variables['input'] = array_merge($variables['input'], [
            'process' => $process->getKey(),
            'stage' => $process->stages->first()->getKey(),
            'grade' => $process->grades->first()->getKey(),
            'period' => $process->periods->first()->getKey(),
            'school' => $process->schools->first()->getKey(),
        ]);

        $this->graphQL($query, $variables)
            ->assertGraphQLErrorMessage('Ocorreu um erro ao finalizar a inscrição')
            ->assertGraphQLValidationError('message', 'Já existe uma inscrição para este(a) aluno(a)');

        config([
            'prematricula.only_one_preregistration_by_student' => false,
        ]);

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $process->id,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_multiple_preregistration(): void
    {
        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariablesForMultiple();

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseCount(PreRegistration::class, 3);
    }

    public function test_multiple_preregistration_with_limit(): void
    {
        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariablesForMultiple();

        $variables['input']['type'] = 'WAITING_LIST';

        $this->process->update([
            'waiting_list_limit' => 2,
        ]);

        $this->graphQL($query, $variables)
            ->assertGraphQLValidationError('message', 'O limite de inscrição em lista de espera já foi atingido');
    }

    public function test_multiple_preregistration_with_limit_in_grouper(): void
    {
        $query = $this->prepareTestAndGetMutation();
        $variables = $this->getVariablesForMultiple();

        $variables['input']['type'] = 'WAITING_LIST';

        $grouper = ProcessGrouperFactory::new()->create([
            'waiting_list_limit' => 2,
        ]);

        $this->process->update([
            'process_grouper_id' => $grouper->getKey(),
        ]);

        $this->graphQL($query, $variables)
            ->assertGraphQLValidationError('message', 'O limite de inscrição em lista de espera já foi atingido');
    }

    public function test_multiple_preregistration_in_multiple_processes_with_limit_in_grouper(): void
    {
        $query = $this->prepareTestAndGetMutation();

        $grouper = ProcessGrouperFactory::new()->create([
            'waiting_list_limit' => 2,
        ]);

        $this->process->update([
            'process_grouper_id' => $grouper->getKey(),
        ]);

        $this->createOpenedProcess();

        $this->process->update([
            'process_grouper_id' => $grouper->getKey(),
        ]);

        $variables = $this->getVariablesForMultiple();
        $variables['input']['type'] = 'WAITING_LIST';

        $this->graphQL($query, $variables)
            ->assertGraphQLValidationError('message', 'O limite de inscrição em lista de espera já foi atingido');
    }

    public function test_multiple_preregistration_with_use_case(): void
    {
        // Caso de uso
        // Processo A: permite apenas 1 inscrição
        // Processo B: permite 2 inscrições
        // Agrupador: permite 2 inscrições
        //
        // Passo 1: Faz a inscrição em Processo A (1 inscrição)
        // Passo 2: Tenta fazer uma nova inscrição em Processo A, mas ele permite apenas 1 inscrição e retorna erro
        // Passo 3: Faz a inscrição em Processo B (2 inscrições)
        // Passo 4: Tenta fazer uma nova inscrição em Processo B, pois ele permite 2 inscrições, porém o agrupador
        //          permite apenas 2 e o aluno já possui duas inscrições

        $query = $this->prepareTestAndGetMutation();
        $variablesFirstProcess = $this->getVariables();
        $firstProcessId = $this->process->getKey();

        $grouper = ProcessGrouperFactory::new()->create([
            'waiting_list_limit' => 2,
        ]);

        $this->process->update([
            'name' => 'Processo A',
            'process_grouper_id' => $grouper->getKey(),
            'waiting_list_limit' => 1,
        ]);

        $this->createOpenedProcess();
        $this->addWaitingListPeriod();

        $variablesSecondProcess = $this->getVariables();
        $secondProcess = $this->process;

        $this->process->update([
            'name' => 'Processo B',
            'process_grouper_id' => $grouper->getKey(),
            'waiting_list_limit' => 2,
        ]);

        $variablesFirstProcess['input']['type'] = 'WAITING_LIST';
        $variablesSecondProcess['input']['type'] = 'WAITING_LIST';

        // Passo 1
        $this->graphQL($query, $variablesFirstProcess)
            ->assertJson([
                'data' => [
                    'preregistrations' => [
                        [
                            'process' => [
                                'id' => $firstProcessId,
                            ],
                        ],
                    ],
                ],
            ]);

        // Passo 2
        $this->graphQL($query, $variablesFirstProcess)
            ->assertGraphQLValidationError('message', 'O limite de inscrição em lista de espera já foi atingido');

        // Passo 3
        $this->graphQL($query, $variablesSecondProcess)
            ->assertJson([
                'data' => [
                    'preregistrations' => [
                        [
                            'process' => [
                                'id' => $secondProcess->id,
                            ],
                        ],
                    ],
                ],
            ]);

        // Passo 4
        $this->graphQL($query, $variablesSecondProcess)
            ->assertGraphQLValidationError('message', 'O limite de inscrição em lista de espera já foi atingido');
    }
}
