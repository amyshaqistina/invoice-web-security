<?php

namespace App\Http\Responses\Auth;

use App\Models\Team;
use Filament\Facades\Filament;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;

class CustomPasswordResetResponse implements PasswordResetResponse
{
    public function toResponse($request): RedirectResponse | Redirector
    {
    //    return redirect(Filament::getUrl()) ;
    return  redirect(url('/guest/login'));
      
    }
}