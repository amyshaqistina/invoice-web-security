<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    // REPLACE THIS LINE:
    // protected $guarded = [];

    // WITH THIS:
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'website',
        'notes',
        'attachment',
        'team_id',
        'created_by',
    ];

    protected $casts = [
    'attachment' => 'array',
    'balance' => 'decimal:2',      // Cast to decimal with 2 places
    'paid_to_date' => 'decimal:2', // Cast to decimal with 2 places
    'last_login' => 'datetime',    // Ensure proper date handling
];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
