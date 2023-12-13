<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int supervisor_user_id
 * @property string street
 * @property string building_number
 * @property string city
 * @property string zip
 */
class Estate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supervisor_user_id',
        'street',
        'building_number',
        'city',
        'zip'
    ];

    public $timestamps = false;

    /**
     * The person managing the property
     *
     * @return BelongsTo
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }
}
