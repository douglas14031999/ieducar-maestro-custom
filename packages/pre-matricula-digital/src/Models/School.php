<?php

namespace iEducar\Packages\PreMatricula\Models;

use iEducar\Packages\PreMatricula\Models\Builders\SchoolBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 * @property int $area_code
 * @property string $phone
 * @property string $email
 * @property Process[] $processes
 * @property Classroom[] $classrooms
 */
class School extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'latitude', 'longitude', 'area_code', 'phone', 'email'];

    public $timestamps = false;

    public function newEloquentBuilder($query): SchoolBuilder
    {
        return new SchoolBuilder($query);
    }

    public function processes()
    {
        return $this->belongsToMany(Process::class, 'process_school');
    }

    /**
     * @codeCoverageIgnore
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'id');
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    /**
     * @codeCoverageIgnore
     *
     * @param  Builder  $query
     * @param  int  $schoolYear
     * @param  int[]  $grades
     * @param  int[]  $periods
     * @return Builder
     */
    public function scopeVacancies($query, $schoolYear, $grades, $periods)
    {
        return $query->withSum([
            'classrooms:vacancy as vacancies' => function ($query) use ($schoolYear, $grades, $periods) {
                return $query->where('school_year_id', $schoolYear)
                    ->whereIn('grade_id', $grades)
                    ->whereIn('period_id', $periods);
            },
        ]);
    }

    public function getGroupedVacancies($schoolYear, $grades, $periods)
    {
        $grades = $this->classrooms()
            ->where('school_year_id', $schoolYear)
            ->whereIn('grade_id', $grades)
            ->whereIn('period_id', $periods)
            ->get()
            ->groupBy('grade_id')
            ->map(function ($grade) {
                return $grade->groupBy('period_id');
            });

        $collection = $this->newCollection();

        foreach ($grades as $periods) {
            foreach ($periods as $classrooms) {
                $vacancies = 0;

                foreach ($classrooms as $classroom) {
                    $vacancies += $classroom->vacancy;
                }

                $collection->add([
                    'grade' => $classroom->grade,
                    'period' => $classroom->period,
                    'school' => $classroom->school,
                    'vacancies' => $vacancies,
                ]);
            }
        }

        return $collection;
    }
}
