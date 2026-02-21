<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $year
 * @property string $start_at
 * @property string $end_at
 */
class SchoolYear extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'year', 'start_at', 'end_at'];

    public $timestamps = false;
}
