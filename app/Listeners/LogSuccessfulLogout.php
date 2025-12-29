<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class LogSuccessfulLogout
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
    public function handle(Logout $event): void
    {

        \Log::info('Logout listener fired for user '.$event->user->id);

        DB::table('sessions')
            ->where('user_id', $event->user->id)
            ->orderByDesc('login_time')   // ✅ fallback to latest session
            ->limit(1)
            ->update([
                'logout_time'   => now(),
                'last_activity' => now(),
            ]);




    }
}
