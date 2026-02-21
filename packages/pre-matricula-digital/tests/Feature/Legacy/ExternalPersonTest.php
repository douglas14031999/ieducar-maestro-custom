<?php

use Database\Factories\LegacyStudentFactory;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateLegacyProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

uses(GraphQLTestCase::class);
uses(CreateLegacyProcess::class);

test('exists external person', function () {
    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addWaitingListPeriod();
    $this->createLegacyData();

    $legacyStudent = LegacyStudentFactory::new()->create();

    $this->preregistration->student->update([
        'external_person_id' => $legacyStudent->ref_idpes,
    ]);

    $query = '
      query preregistrationByProtocol(
        $protocol: String!
      ) {
        preregistration: preregistrationByProtocol(protocol: $protocol) {
          id
          student {
            student_name: name
            student_cpf: cpf
          }
          external {
            id
            name
          }
        }
      }
    ';

    $this->graphQL($query, [
        'protocol' => $this->preregistration->protocol,
    ])->assertJson([
        'data' => [
            'preregistration' => [
                'id' => $this->preregistration->getKey(),
                'student' => [
                    'student_name' => $this->student->name,
                    'student_cpf' => $this->student->cpf,
                ],
                'external' => [
                    'id' => $legacyStudent->ref_idpes,
                    'name' => $legacyStudent->name,
                ],
            ],
        ],
    ]);
});
