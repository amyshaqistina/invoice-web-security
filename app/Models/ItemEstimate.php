<?php

namespace App\Models;

use Livewire\Livewire;
use App\Events\ItemChangedEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Broadcast;

class ItemEstimate extends Model
{

    use HasFactory;
    protected $guarded = ['id'];

    // protected static function booted()
    // {

    //     static::created(function ($pivot) {
    //         event(new ItemChangedEvent($pivot->item_id, $pivot->estimate_id));
            
    //     });

    //     static::deleted(function ($pivot) {
    //         event(new ItemChangedEvent($pivot->item_id, $pivot->estimate_id));
            
    //     });

    //     static::updated(function ($pivot) {
    //         event(new ItemChangedEvent($pivot->item_id, $pivot->estimate_id));
            
    //     });
    // }
}
