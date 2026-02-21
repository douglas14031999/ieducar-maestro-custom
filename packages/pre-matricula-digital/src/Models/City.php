<?php

namespace iEducar\Packages\PreMatricula\Models;

use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 */
class City extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'name'];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function scopeSearchCity($builder, $search)
    {
        $id = intval($search);
        $name = "$search%";

        return $builder->where(
            fn (Builder $query) => $query->whereRaw('unaccent(name) ilike ?', [$name])->orWhere('id', $id)
        );
    }
}
