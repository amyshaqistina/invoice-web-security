<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_payments');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
