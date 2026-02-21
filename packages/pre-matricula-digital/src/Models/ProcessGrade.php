<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessGrade extends Model
{
    protected $table = 'process_grade';

    protected $fillable = [
        'process_id',
        'period_id',
    ];

    public $timestamps = false;
}
