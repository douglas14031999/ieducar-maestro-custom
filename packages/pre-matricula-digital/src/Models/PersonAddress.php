<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $person_id
 * @property string $address
 * @property string $number
 * @property string $complement
 * @property string $neighborhood
 * @property string $city
 * @property float $latitude
 * @property float $longitude
 * @property Person $person
 * @property bool $manual_change_location
 */
class PersonAddress extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'person_id',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'latitude',
        'longitude',
        'manual_change_location',
        'postal_code',
    ];

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
