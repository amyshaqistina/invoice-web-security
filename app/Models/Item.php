<?php

namespace App\Models;

use Livewire\Livewire;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'product_image' => 'array',
        'attachment' => 'array', // Converts JSON to array automatically
    ];


    public function Estimates()
    {
        return $this->belongsToMany(Estimate::class, 'item_estimate');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'item_invoice');
    }

    public function recurringInvoices()
    {
        return $this->belongsToMany(RecurringInvoice::class, 'item_recurringinvoices');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function categories()
    {
        return $this->belongsToMany(
            ItemCategory::class,
            'item_category_items',
            'item_id',            // Foreign key on pivot table referencing the current model
            'item_category_id'    // Foreign key on pivot table referencing the related model
        );
    }


    public function variations()
    {
        return $this->hasMany(VariationItem::class);
    }


}
