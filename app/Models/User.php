<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int user_id
 * @property string user_firstname
 * @property string user_lastname
 */
class User extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_firstname',
        'user_lastname',
    ];

    /**
     * Properties/estates supervised by the user
     *
     * @return HasMany
     */
    public function estates(): HasMany
    {
        return $this->hasMany(Estate::class, 'supervisor_user_id');
    }

    /**
     * Information about when the user is taking a break
     *
     * @return HasMany
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(UserShift::class, 'user_id');
    }

    /**
     * Information about whom the user will replace another user during a break
     *
     * @return HasMany
     */
    public function shiftSubstitution(): HasMany
    {
        return $this->hasMany(UserShift::class, 'substitute_user_id');
    }
}
