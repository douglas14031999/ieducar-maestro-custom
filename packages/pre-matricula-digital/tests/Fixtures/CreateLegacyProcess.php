<?php

namespace iEducar\Packages\PreMatricula\Tests\Fixtures;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacyIndividual;
use App\Models\LegacyPeriod;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolGrade;
use App\Models\LegacyStudent;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Database\Eloquent\Model;

trait CreateLegacyProcess
{
    use CreateSimpleProcess;

    protected LegacyPeriod $legacyPeriod;

    protected LegacyGrade $legacyGrade;

    protected LegacySchoolGrade $legacySchoolGrade;

    protected LegacySchoolClass $legacySchoolClass;

    protected LegacyIndividual $legacyIndividual;

    protected LegacyStudent $legacyStudent;

    protected LegacyRegistration $legacyRegistrationLastYear;

    protected LegacySchoolClass $legacySchoolClassWithRegistration;

    public function createLegacyData(): void
    {
        $classroom = $this->school->classrooms()->first();

        Model::unguarded(function () use ($classroom) {
            $legacySchoolAttributes = LegacySchoolFactory::new()->withName($this->preregistration->school->name)->make([
                'cod_escola' => $this->preregistration->school->id,
            ])->getAttributes();

            $legacySchool = LegacySchool::query()->create($legacySchoolAttributes);

            $legacyCourseAttributes = LegacyCourseFactory::new()->make([
                'cod_curso' => $this->preregistration->grade->course->id,
                'nm_curso' => $this->preregistration->grade->course->name,
            ])->getAttributes();

            $legacyCourse = LegacyCourse::query()->create($legacyCourseAttributes);

            $legacyGradeAttributes = LegacyGradeFactory::new()->make([
                'cod_serie' => $this->preregistration->grade->id,
                'nm_serie' => $this->preregistration->grade->name,
                'descricao' => null,
                'ref_cod_curso' => $legacyCourse,
                'importar_serie_pre_matricula' => true,
            ])->getAttributes();

            /** @var LegacyGrade $legacyGrade */
            $legacyGrade = LegacyGrade::query()->create($legacyGradeAttributes);
            $this->legacyGrade = $legacyGrade;

            LegacySchoolCourseFactory::new()->create([
                'ref_cod_escola' => $legacySchool,
                'ref_cod_curso' => $legacyCourse,
                'anos_letivos' => '{' . $classroom->schoolYear->year . '}',
            ]);

            $this->legacySchoolGrade = LegacySchoolGradeFactory::new()->create([
                'ref_cod_escola' => $legacySchool,
                'ref_cod_serie' => $legacyGrade,
                'anos_letivos' => '{' . $classroom->schoolYear->year . '}',
            ]);

            $this->legacyPeriod = LegacyPeriodFactory::new()->create([
                'id' => $this->period->id,
                'nome' => $this->period->name,
            ]);

            $legacySchoolClassAttributes = LegacySchoolClassFactory::new()->make([
                'cod_turma' => $classroom->getKey(),
                'ref_ref_cod_escola' => $legacySchool,
                'ref_ref_cod_serie' => $legacyGrade,
                'ref_cod_curso' => $legacyCourse,
                'max_aluno' => $classroom->vacancy,
                'ano' => $classroom->schoolYear->year,
                'turma_turno_id' => $this->legacyPeriod->id,
            ])->getAttributes();

            $this->legacySchoolClass = LegacySchoolClass::query()->create($legacySchoolClassAttributes);
        });
    }

    public function createLegacyPersonData(): void
    {
        $this->legacyIndividual = LegacyIndividualFactory::new()
            ->withName($this->student->name)
            ->withDocument(rg: $this->student->rg, birthCertificate: $this->student->birth_certificate)
            ->create([
                'data_nasc' => $this->student->date_of_birth,
            ]);

        $this->legacyStudent = LegacyStudentFactory::new()->create([
            'ref_idpes' => $this->legacyIndividual,
        ]);
    }

    public function createLegacyRegistrationCurrentYear(): void
    {
        $this->legacySchoolClassWithRegistration = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $this->legacySchoolGrade->school_id,
            'ref_ref_cod_serie' => $this->legacyGrade->id,
            'ref_cod_curso' => $this->legacyGrade->course_id,
            'ano' => $this->legacySchoolClass->year,
        ]);

        $this->legacyRegistrationLastYear = LegacyRegistrationFactory::new()
            ->withStudent($this->legacyStudent)
            ->withEnrollment($this->legacySchoolClassWithRegistration)
            ->create([
                'ano' => $this->legacySchoolClass->year,
            ]);
    }

    public function createLegacyRegistrationLastYear(): void
    {
        $this->legacySchoolClassWithRegistration = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $this->legacySchoolGrade->school_id,
            'ref_ref_cod_serie' => $this->legacyGrade->id,
            'ref_cod_curso' => $this->legacyGrade->course_id,
            'ano' => $this->legacySchoolClass->year - 1,
        ]);

        $this->legacyRegistrationLastYear = LegacyRegistrationFactory::new()
            ->withStudent($this->legacyStudent)
            ->withEnrollment($this->legacySchoolClassWithRegistration)
            ->create([
                'ano' => $this->legacySchoolClass->year - 1,
            ]);
    }
}
