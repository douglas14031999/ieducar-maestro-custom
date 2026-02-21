<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @codeCoverageIgnore
 *
 * @property int $id
 * @property int $user_type_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property UserType $userType
 */
class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    /**
     * @var array
     */
    protected $fillable = ['user_type_id', 'name', 'email', 'password'];

    public $timestamps = false;

    protected $table = 'user';

    /**
     * @return BelongsTo
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class);
    }
}
