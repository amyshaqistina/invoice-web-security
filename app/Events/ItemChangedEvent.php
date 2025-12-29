<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $itemId;
    public $estimateId;

    /**
     * Create a new event instance.
     */
    public function __construct($itemId, $estimateId)
    {
        //
        $this->itemId = $itemId;
        $this->estimateId = $estimateId;
    }

  
}
