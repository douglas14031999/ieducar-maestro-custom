<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
class Course extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name'];

    public $timestamps = false;

    /**
     * @codeCoverageIgnore
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
