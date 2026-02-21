<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $text
 */
class Notice extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['text'];
}
