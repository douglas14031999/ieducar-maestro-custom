<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
class UserType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name'];
}
