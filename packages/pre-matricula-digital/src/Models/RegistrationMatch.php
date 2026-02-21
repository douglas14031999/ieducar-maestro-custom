<?php

namespace iEducar\Packages\PreMatricula\Models;

use DateTime;
use iEducar\Packages\PreMatricula\Models\Builders\RegistrationMatchBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property DateTime $birthdate
 *
 * @method static RegistrationMatchBuilder query()
 */
class RegistrationMatch extends Model
{
    protected $table = 'registration_match';

    public function newEloquentBuilder($query): RegistrationMatchBuilder
    {
        return new RegistrationMatchBuilder($query);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getInitialsAttribute()
    {
        return collect(explode(' ', $this->name))
            ->reject(function ($namePart) {
                return strlen($namePart) <= 2;
            })
            ->map(function ($namePart) {
                return mb_substr(trim($namePart), 0, 1) . '.';
            })->implode('');
    }
}
