<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $age
 * @property int $course_id
 */
class ProcessGradeSuggest extends Model
{
    protected $table = 'public.process_grade_suggest';
}
