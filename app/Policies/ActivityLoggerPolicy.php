<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityLoggerPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    // public function viewany(): Response
    // {
    //     return in_array('admin', auth()->user()->roles->pluck('name')->toArray())
    //                 ? Response::allow()
    //                 : Response::deny('You do not own this post.');
    // }
}
