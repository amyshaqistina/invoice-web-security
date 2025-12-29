<?php

namespace App\Listeners;

use Livewire\Livewire;
use App\Events\ItemChangedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ItemChangedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ItemChangedEvent $event): void
    {
         Livewire::dispatch('orderCreated', $event);
    }
}
