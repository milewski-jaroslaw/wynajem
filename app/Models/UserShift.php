<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int user_id
 * @property int substitute_user_id
 * @property string temp_changes
 * @property Carbon date_from
 * @property Carbon date_to
 */
class UserShift extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'substitute_user_id',
        'temp_changes',
        'date_from',
        'date_to',
    ];

    public $timestamps = false;
    public $table = 'users_shifts';

    protected $casts = [
        'temp_changes' => 'json',
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    /**
     * User managing the property/estate
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The user who will replace the user in his absence
     *
     * @return BelongsTo
     */
    public function substituteUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'substitute_user_id');
    }
}
