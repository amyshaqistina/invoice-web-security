<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'attachment' => 'array', // Converts JSON to array automatically
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
