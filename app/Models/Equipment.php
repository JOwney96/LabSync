<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tag_id',
        'category',
        'status',
        'location',
        'calibration_due',
        'purchase_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'calibration_due' => 'date',
        'purchase_date' => 'date',
    ];

    // --- Relationships ---
    public function checkoutRequests(): HasMany
    {
        return $this->hasMany(CheckoutRequest::class);
    }
}
