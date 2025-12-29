<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;


class LogoutResponse implements Responsable
{


    public function toResponse($request): RedirectResponse
    {
        // if (filament()->getCurrentPanel()->getPath()) {
        //     dd(filament()->getTenant());
        //     return redirect()->intended(url("/guest/".filament()->getTenant()->slug."/login"));
        // }

        // change this to your desired route
        return redirect()->intended(filament()->getLoginUrl());
        // return redirect()->intended(url('/guest/login'));
    }
}
