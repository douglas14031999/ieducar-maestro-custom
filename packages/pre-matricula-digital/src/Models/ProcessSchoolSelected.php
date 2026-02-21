<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessSchoolSelected extends Model
{
    protected $table = 'process_school_selected';

    protected $fillable = [
        'process_id',
        'school_id',
    ];

    public $timestamps = false;
}
