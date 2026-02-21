<?php

namespace iEducar\Packages\PreMatricula\Models;

use iEducar\Packages\PreMatricula\Models\Builders\PreRegistrationBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $period_id
 * @property int $school_id
 * @property int $grade_id
 * @property int $school_year_id
 * @property int $student_id
 * @property int $responsible_id
 * @property int $process_stage_type_id
 * @property int $external_person_id
 * @property int $status
 * @property int $position
 * @property int $classroom_id
 * @property string $observation
 * @property Person $student
 * @property Person $responsible
 * @property SchoolYear $schoolYear
 * @property Grade $grade
 * @property School $school
 * @property Period $period
 * @property Process $process
 * @property PreRegistrationField[] $fields
 * @property Timeline[] $timeline
 *
 * @method static PreRegistrationBuilder query()
 */
class PreRegistration extends Model
{
    public const REGISTRATION_RENEWAL = 1;

    public const REGISTRATION = 2;

    public const WAITING_LIST = 3;

    public const STATUS_WAITING = 1;

    public const STATUS_ACCEPTED = 2;

    public const STATUS_REJECTED = 3;

    public const STATUS_SUMMONED = 4;

    public const STATUS_IN_CONFIRMATION = 5;

    public const RELATION_MOTHER = 1;

    public const RELATION_FATHER = 2;

    public const RELATION_GUARDIAN = 3;

    public const RELATION_SELF = 4;

    /**
     * @var string
     */
    protected $table = 'preregistrations';

    /**
     * @var array
     */
    protected $fillable = [
        'preregistration_type_id',
        'parent_id',
        'process_id',
        'process_stage_id',
        'period_id',
        'school_id',
        'grade_id',
        'student_id',
        'responsible_id',
        'relation_type_id',
        'status',
        'observation',
        'protocol',
        'code',
        'priority',
        'external_person_id',
        'in_classroom_id',
        'summoned_at',
    ];

    protected function casts(): array
    {
        return [
            'summoned_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        parent::booted();

        static::saving(function ($model) {
            if ($model->isDirty('status') && $model->status === self::STATUS_SUMMONED && !$model->isDirty('summoned_at')) {
                $model->summoned_at = now();
            }
        });
    }

    public function newEloquentBuilder($query)
    {
        return new PreRegistrationBuilder($query);
    }

    /**
     * @return void
     */
    public function returnToWait()
    {
        $this->status = self::STATUS_WAITING;
    }

    /**
     * @return void
     */
    public function updateGrade($id)
    {
        $this->grade_id = $id;
    }

    /**
     * @return void
     */
    public function accept()
    {
        $this->status = self::STATUS_ACCEPTED;
    }

    /**
     * @return void
     */
    public function reject($justification = null)
    {
        if ($justification) {
            $this->observation = "{$justification}\n{$this->observation}";
        }

        $this->status = self::STATUS_REJECTED;
    }

    public function summon($justification = null)
    {
        if ($justification) {
            $this->observation = "{$justification}\n{$this->observation}";
        }

        $this->status = self::STATUS_SUMMONED;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(PreRegistration::class, 'parent_id');
    }

    /**
     * @codeCoverageIgnore
     *
     * @return HasOne
     */
    public function waiting()
    {
        return $this->hasOne(PreRegistration::class, 'parent_id');
    }

    /**
     * @codeCoverageIgnore
     *
     * @return HasMany
     */
    public function others()
    {
        return $this->hasMany(PreRegistration::class, 'process_id', 'process_id')
            ->join('people', 'people.id', '=', 'preregistrations.student_id')
            ->where('preregistrations.id', '!=', $this->id)
            ->where('preregistrations.id', '!=', $this->parent?->id)
            ->where('preregistrations.id', '!=', $this->waiting?->id)
            ->whereNotNull('people.cpf')
            ->where('people.cpf', $this->student->cpf);
    }

    /**
     * @return BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Person::class, 'student_id');
    }

    /**
     * @return BelongsTo
     */
    public function responsible()
    {
        return $this->belongsTo(Person::class, 'responsible_id');
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    /**
     * @return BelongsTo
     */
    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(PreRegistrationField::class, 'preregistration_id');
    }

    /**
     * @codeCoverageIgnore
     */
    public function preregistrationFields(): HasMany
    {
        return $this->hasMany(PreRegistrationField::class);
    }

    /**
     * @codeCoverageIgnore
     */
    public function linked(): HasMany
    {
        return $this->hasMany(PreRegistrationLinkedByEmail::class, 'preregistration_id');
    }

    /**
     * @codeCoverageIgnore
     */
    public function scopeAccepted($builder)
    {
        return $builder->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * @codeCoverageIgnore
     */
    public function scopeRejected($builder)
    {
        return $builder->where('status', self::STATUS_REJECTED);
    }

    public function scopeWaiting($builder)
    {
        return $builder->where('status', self::STATUS_WAITING);
    }

    public function scopeNextInLine($builder)
    {
        return $builder->where('preregistration_position.position', 1);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function stage()
    {
        return $this->belongsTo(ProcessStage::class, 'process_stage_id');
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * @codeCoverageIgnore
     */
    public function inClassroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'in_classroom_id');
    }

    public function external(): HasOne
    {
        return $this->hasOne(ExternalPerson::class, 'preregistration_id', 'id');
    }

    /**
     * @codeCoverageIgnore
     */
    public function isRegistrationRenewal()
    {
        return $this->preregistration_type_id === self::REGISTRATION_RENEWAL;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isRegistration()
    {
        return $this->preregistration_type_id === self::REGISTRATION;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isWaitingList()
    {
        return $this->preregistration_type_id === self::WAITING_LIST;
    }

    /**
     * @return MorphMany
     */
    public function timeline()
    {
        return $this->morphMany(Timeline::class, 'model');
    }
}
