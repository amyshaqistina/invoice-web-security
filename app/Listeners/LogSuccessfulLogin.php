<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class LogSuccessfulLogin
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
    public function handle(Login $event): void
    {
        \Log::info('Login event fired for user: '.$event->user->id);

        DB::table('sessions')->insert([
            'user_id'      => $event->user->id,   // ✅ always valid
            'session_id'   => session()->getId(),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->header('User-Agent'),
            'login_time'   => now(),
            'last_activity'=> now(),
        ]);

    }
}
