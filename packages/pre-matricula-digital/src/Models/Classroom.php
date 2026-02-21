<?php

namespace iEducar\Packages\PreMatricula\Models;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolClassStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $period_id
 * @property int $school_id
 * @property int $grade_id
 * @property int $school_year_id
 * @property string $name
 * @property int $vacancy
 * @property SchoolYear $schoolYear
 * @property Grade $grade
 * @property School $school
 * @property Period $period
 */
class Classroom extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'period_id',
        'school_id',
        'grade_id',
        'school_year_id',
        'name',
        'vacancy',
        'available_vacancies',
        'available',
        'multi',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * @return BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo
     */
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_vacancies', '>=', 1);
    }

    /**
     * Retorna a data inicial do ano letivo.
     *
     * TODO remover quando separar do i-Educar
     *
     * @codeCoverageIgnore
     *
     * @return Carbon
     */
    public function getBeginAcademicYear()
    {
        $stages = $this->hasMany(LegacySchoolClassStage::class, 'ref_cod_turma')->orderBy('sequencial')->get();

        if ($stages->isEmpty()) {
            $stages = $this->hasMany(LegacyAcademicYearStage::class, 'ref_ref_cod_escola', 'school_id')
                ->orderBy('sequencial')
                ->where('ref_ano', $this->school_year_id)
                ->get();
        }

        $stage = $stages->first();

        if ($stages->isEmpty()) {
            return now();
        }

        return $stage->data_inicio;
    }

    /**
     * Retorna a data final do ano letivo.
     *
     * TODO remover quando separar do i-Educar
     *
     * @codeCoverageIgnore
     *
     * @return Carbon|null
     */
    public function getEndAcademicYear()
    {
        $stages = $this->hasMany(LegacySchoolClassStage::class, 'ref_cod_turma')->orderByDesc('sequencial')->get();

        if ($stages->isEmpty()) {
            $stages = $this->hasMany(LegacyAcademicYearStage::class, 'ref_ref_cod_escola', 'school_id')
                ->orderByDesc('sequencial')
                ->where('ref_ano', $this->school_year_id)
                ->get();
        }

        $stage = $stages->first();

        if ($stages->isEmpty()) {
            return null;
        }

        return $stage->data_fim;
    }
}
