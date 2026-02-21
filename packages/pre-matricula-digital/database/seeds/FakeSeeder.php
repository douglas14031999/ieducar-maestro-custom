<?php

namespace iEducar\Packages\PreMatricula\Database\Seeds;

use Carbon\Carbon;
use iEducar\Packages\PreMatricula\Models\Classroom;
use iEducar\Packages\PreMatricula\Models\Course;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\Grade;
use iEducar\Packages\PreMatricula\Models\Period;
use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Models\ProcessField;
use iEducar\Packages\PreMatricula\Models\ProcessStage;
use iEducar\Packages\PreMatricula\Models\School;
use iEducar\Packages\PreMatricula\Models\SchoolYear;
use Illuminate\Database\Seeder;

class FakeSeeder extends Seeder
{
    private function periods()
    {
        Period::query()->create(['name' => 'Matutino']);
        Period::query()->create(['name' => 'Vespertino']);
        Period::query()->create(['name' => 'Noturno']);
        Period::query()->create(['name' => 'Integral']);
    }

    private function courses()
    {
        Course::query()->create(['name' => 'Educação Infantil']);
        Course::query()->create(['name' => 'Ensino Fundamental']);
        Course::query()->create(['name' => 'EJA']);
    }

    private function grades()
    {
        $educacaoInfantil = Course::query()->where('name', 'Educação Infantil')->first()->getKey();
        $ensinoFundamental = Course::query()->where('name', 'Ensino Fundamental')->first()->getKey();
        $eja = Course::query()->where('name', 'EJA')->first()->getKey();

        Grade::query()->create(['course_id' => $educacaoInfantil, 'name' => 'Berçario', 'start_birth' => '2020-03-31', 'end_birth' => '2018-03-31']);
        Grade::query()->create(['course_id' => $educacaoInfantil, 'name' => 'Maternal', 'start_birth' => '2018-03-31', 'end_birth' => '2016-03-31']);
        Grade::query()->create(['course_id' => $educacaoInfantil, 'name' => 'Jardim', 'start_birth' => '2016-03-31', 'end_birth' => '2014-03-31']);

        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '1º ano', 'start_birth' => '2014-03-31', 'end_birth' => '2013-03-31']);
        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '2º ano', 'start_birth' => '2013-03-31', 'end_birth' => '2012-03-31']);
        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '3º ano', 'start_birth' => '2012-03-31', 'end_birth' => '2011-03-31']);
        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '4º ano', 'start_birth' => '2011-03-31', 'end_birth' => '2010-03-31']);
        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '5º ano', 'start_birth' => '2010-03-31', 'end_birth' => '2009-03-31']);
        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '6º ano', 'start_birth' => '2009-03-31', 'end_birth' => '2008-03-31']);
        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '7º ano', 'start_birth' => '2008-03-31', 'end_birth' => '2007-03-31']);
        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '8º ano', 'start_birth' => '2007-03-31', 'end_birth' => '2006-03-31']);
        Grade::query()->create(['course_id' => $ensinoFundamental, 'name' => '9º ano', 'start_birth' => '2006-03-31', 'end_birth' => '2005-03-31']);

        Grade::query()->create(['course_id' => $eja, 'name' => '1º módulo']);
        Grade::query()->create(['course_id' => $eja, 'name' => '2º módulo']);
        Grade::query()->create(['course_id' => $eja, 'name' => '3º módulo']);
    }

    private function schools()
    {
        School::query()->create([
            'name' => 'Escola Adoleta',
            'latitude' => -28.686264,
            'longitude' => -49.350765,
        ]);

        School::query()->create([
            'name' => 'Escola Borboleta',
            'latitude' => -28.678461,
            'longitude' => -49.345606,
        ]);

        School::query()->create([
            'name' => 'Escola do Céu',
            'latitude' => -28.678152,
            'longitude' => -49.351627,
        ]);

        School::query()->create([
            'name' => 'Escola Primeira Infância',
            'latitude' => -28.681892,
            'longitude' => -49.341286,
        ]);
    }

    private function schoolYear()
    {
        SchoolYear::query()->create([
            'name' => 'Ensino Fundamental',
            'year' => 2021,
            'start_at' => Carbon::create(2021, 01, 01),
            'end_at' => Carbon::create(2021, 12, 31),
        ]);
    }

    private function classrooms()
    {
        $schoolYear = SchoolYear::query()->first();
        $schools = School::query()->get();
        $grades = Grade::query()->get();
        $periods = Period::query()->get();

        foreach ($schools as $school) {
            foreach ($grades as $grade) {
                foreach ($periods as $period) {
                    Classroom::query()->create([
                        'period_id' => $period->getKey(),
                        'school_id' => $school->getKey(),
                        'grade_id' => $grade->getKey(),
                        'school_year_id' => $schoolYear->getKey(),
                        'name' => $grade->name . ' A - ' . $period->name,
                        'vacancy' => 25,
                        'available_vacancies' => 25,
                    ]);

                    Classroom::query()->create([
                        'period_id' => $period->getKey(),
                        'school_id' => $school->getKey(),
                        'grade_id' => $grade->getKey(),
                        'school_year_id' => $schoolYear->getKey(),
                        'name' => $grade->name . ' B - ' . $period->name,
                        'vacancy' => 25,
                        'available_vacancies' => 25,
                    ]);
                }
            }
        }
    }

    public function process()
    {
        $schools = School::query()->get();
        $grades = Grade::query()->get();
        $periods = Period::query()->get();
        $fields = Field::query()->get();

        /** @var Process $process */
        $process = Process::query()->create([
            'school_year_id' => SchoolYear::query()->first()->getKey(),
            'name' => 'Ensino Fundamental',
        ]);

        foreach ($schools as $school) {
            // TODO substituir
            // $process->schools()->attach($school);
        }

        foreach ($grades as $grade) {
            $process->grades()->attach($grade);
        }

        foreach ($periods as $period) {
            $process->periods()->attach($period);
        }

        foreach ($fields as $key => $field) {
            ProcessField::query()->create([
                'process_id' => $process->getKey(),
                'field_id' => $field->getKey(),
                'required' => false,
                'order' => $key + 1,
                'weight' => ($key + 1) * 100,
            ]);
        }

        ProcessStage::query()->create([
            'process_id' => $process->getKey(),
            'process_stage_type_id' => ProcessStage::TYPE_REGISTRATION_RENEWAL,
            'name' => 'Rematrícula',
            'description' => 'Rematrícula',
            'start_at' => now(),
            'end_at' => now()->addYear(),
        ]);

        ProcessStage::query()->create([
            'process_id' => $process->getKey(),
            'process_stage_type_id' => ProcessStage::TYPE_REGISTRATION,
            'name' => 'Matrícula',
            'description' => 'Matrícula',
            'start_at' => now(),
            'end_at' => now()->addYear(),
        ]);

        ProcessStage::query()->create([
            'process_id' => $process->getKey(),
            'process_stage_type_id' => ProcessStage::TYPE_WAITING_LIST,
            'name' => 'Lista de espera',
            'description' => 'Lista de espera',
            'start_at' => now(),
            'end_at' => now()->addYear(),
        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->schoolYear();
        $this->schools();
        $this->courses();
        $this->grades();
        $this->periods();
        $this->classrooms();
        $this->process();
    }
}
