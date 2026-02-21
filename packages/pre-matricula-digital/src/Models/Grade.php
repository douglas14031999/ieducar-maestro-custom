<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $age
 * @property int $course_id
 */
class Grade extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'age', 'course_id', 'start_birth', 'end_birth'];

    public $timestamps = false;

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @codeCoverageIgnore
     */
    public function processes()
    {
        return $this->belongsToMany(Process::class, 'process_grade');
    }
}
