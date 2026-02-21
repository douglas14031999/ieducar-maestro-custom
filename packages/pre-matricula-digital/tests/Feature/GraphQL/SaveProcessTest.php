<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\GradeFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PeriodFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class SaveProcessTest extends GraphQLTestCase
{
    public function test_create(): void
    {
        $process = ProcessFactory::new()->make();
        $grade = GradeFactory::new()->create();
        $period = PeriodFactory::new()->create();

        $query = '
            mutation save(
                $input: ProcessInput!
            ) {
                process: saveProcess(
                    input: $input
                ) {
                    name
                    active
                    schoolYear {
                        year
                    }
                    periods {
                        id
                    }
                    messageFooter
                    gradeAgeRangeLink
                    forceSuggestedGrade
                    showPriorityProtocol
                    allowResponsibleSelectMapAddress
                    blockIncompatibleAgeGroup
                    autoRejectByDays
                    autoRejectDays
                    selectedSchools
                    waitingListLimit
                    onePerYear
                    showWaitingList
                    rejectType
                    criteria
                }
            }
        ';

        $this->graphQL($query, [
            'input' => [
                'id' => null,
                'name' => $process->name,
                'active' => $process->active,
                'schoolYear' => $process->school_year_id,
                'grades' => [$grade->getKey()],
                'periods' => [$period->getKey()],
                'messageFooter' => $process->message_footer,
                'gradeAgeRangeLink' => $process->grade_age_range_link,
                'forceSuggestedGrade' => $process->force_suggested_grade,
                'showPriorityProtocol' => $process->show_priority_protocol,
                'allowResponsibleSelectMapAddress' => $process->allow_responsible_select_map_address,
                'blockIncompatibleAgeGroup' => $process->block_incompatible_age_group,
                'autoRejectByDays' => $process->auto_reject_by_days,
                'autoRejectDays' => $process->auto_reject_days,
                'selectedSchools' => $process->selected_schools,
                'schools' => [],
                'waitingListLimit' => $process->waiting_list_limit,
                'onePerYear' => $process->one_per_year,
                'showWaitingList' => $process->show_waiting_list,
                'rejectType' => 'NO_REJECT',
                'criteria' => $process->criteria,
            ],
        ])->assertJson([
            'data' => [
                'process' => [
                    'name' => $process->name,
                    'active' => $process->active,
                    'schoolYear' => [
                        'year' => $process->schoolYear->year,
                    ],
                    'periods' => [
                        [
                            'id' => $period->getKey(),
                        ],
                    ],
                    'messageFooter' => $process->message_footer,
                    'gradeAgeRangeLink' => $process->grade_age_range_link,
                    'forceSuggestedGrade' => $process->force_suggested_grade,
                    'showPriorityProtocol' => $process->show_priority_protocol,
                    'allowResponsibleSelectMapAddress' => $process->allow_responsible_select_map_address,
                    'blockIncompatibleAgeGroup' => $process->block_incompatible_age_group,
                    'autoRejectByDays' => $process->auto_reject_by_days,
                    'autoRejectDays' => $process->auto_reject_days,
                    'selectedSchools' => $process->selected_schools,
                    'waitingListLimit' => $process->waiting_list_limit,
                    'onePerYear' => $process->one_per_year,
                    'showWaitingList' => $process->show_waiting_list,
                    'rejectType' => 'NO_REJECT',
                    'criteria' => $process->criteria,
                ],
            ],
        ]);
    }

    public function test_update(): void
    {
        [$response, $process] = $this->generateBasicTest();

        $response->assertJson([
            'data' => [
                'process' => [
                    'id' => $process->getKey(),
                    'name' => 'Processo Teste',
                    'active' => true,
                    'messageFooter' => 'Mensagem do Rodapé',
                    'gradeAgeRangeLink' => 'Link',
                    'forceSuggestedGrade' => false,
                    'showPriorityProtocol' => false,
                    'allowResponsibleSelectMapAddress' => false,
                    'blockIncompatibleAgeGroup' => false,
                    'selectedSchools' => $process->selected_schools,
                    'waitingListLimit' => $process->waiting_list_limit,
                    'onePerYear' => $process->one_per_year,
                    'showWaitingList' => false,
                    'rejectType' => 'NO_REJECT',
                ],
            ],
        ]);
    }

    public function test_update_minha_vaga_na_creche(): void
    {
        config([
            'prematricula.minha-vaga-na-creche' => [
                'url' => 'https://minhavaganacreche.com.br/api/city/external',
                'token' => 'super-secret',
            ],
        ]);

        Http::fake();

        $this->generateBasicTest();

        Http::assertNothingSent();
    }

    public function test_update_minha_vaga_na_creche_request(): void
    {
        config([
            'prematricula.minha_vaga_na_creche.url' => 'https://minhavaganacreche.com.br/api/city/external',
            'prematricula.minha_vaga_na_creche.token' => 'super-secret',
            'prematricula.city' => 'Içara',
            'prematricula.state' => 'SC',

        ]);

        Http::fake();

        $this->generateBasicTest();

        Http::assertSent(function (Request $request) {
            return $request['city'] === 'Içara'
                && $request['state'] === 'SC'
                && $request->url() === 'https://minhavaganacreche.com.br/api/city/external';
        });
    }

    private function generateBasicTest()
    {
        $process = ProcessFactory::new()->complete()->create();

        $query = '
            mutation save(
                $input: ProcessInput!
            ) {
                process: saveProcess(
                    input: $input
                ) {
                    id
                    name
                    active
                    messageFooter
                    gradeAgeRangeLink
                    forceSuggestedGrade
                    showPriorityProtocol
                    allowResponsibleSelectMapAddress
                    blockIncompatibleAgeGroup
                    autoRejectByDays
                    autoRejectDays
                    selectedSchools
                    waitingListLimit
                    onePerYear
                    showWaitingList
                    rejectType
                    criteria
                }
            }
        ';

        $response = $this->graphQL($query, [
            'input' => [
                'id' => $process->getKey(),
                'name' => 'Processo Teste',
                'active' => true,
                'schoolYear' => $process->school_year_id,
                'grades' => $process->grades->pluck('id'),
                'periods' => $process->periods->pluck('id'),
                'messageFooter' => 'Mensagem do Rodapé',
                'gradeAgeRangeLink' => 'Link',
                'forceSuggestedGrade' => false,
                'showPriorityProtocol' => false,
                'allowResponsibleSelectMapAddress' => false,
                'blockIncompatibleAgeGroup' => false,
                'autoRejectByDays' => false,
                'autoRejectDays' => null,
                'selectedSchools' => $process->selected_schools,
                'schools' => [],
                'waitingListLimit' => $process->waiting_list_limit,
                'onePerYear' => $process->one_per_year,
                'showWaitingList' => false,
                'rejectType' => 'NO_REJECT',
                'criteria' => 'Critérios de Classificação',
            ],
        ]);

        return [$response, $process, $query];
    }
}
