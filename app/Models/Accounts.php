<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accounts extends Model
{
    use HasFactory;
    protected $guarded = [];


    /**
     * Get the status that owns the Accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(AccountStatuses::class, 'status_id', 'id');
    }

    /**
     * Get all of the cards for the Accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cards()
    {
        return $this->hasMany(Cards::class, 'account_id', 'id');
    }

    /**
     * Get the user that owns the Accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
