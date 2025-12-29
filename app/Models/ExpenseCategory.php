<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
