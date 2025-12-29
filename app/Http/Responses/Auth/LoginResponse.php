<?php

namespace App\Http\Responses\Auth;

use App\Models\Team;
use Filament\Facades\Filament;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
    //    return redirect(Filament::getUrl()) ;

    if(auth()->user()->hasRole(['superadmin'])){
       
        return  redirect(url('/admin'));
    }
    return  redirect(filament()->getPanel('main')->getLoginUrl());

    
        if(in_array('admin', auth()->user()->roles->pluck('name')->toArray())){
            return redirect(url('/admin'));
        }else if(in_array('staff', auth()->user()->roles->pluck('name')->toArray())){
            return redirect(url('/admin'));
        }else if(in_array('ccd', auth()->user()->roles->pluck('name')->toArray())){
            return redirect(url('/admin'));
        }else if(in_array('ceo', auth()->user()->roles->pluck('name')->toArray())){
            return redirect(url('/admin'));
        }else if(in_array('external', auth()->user()->roles->pluck('name')->toArray())){
            return redirect(url('/external'));
        }else{
            return redirect()->intended(Filament::getUrl());
        }
      
    }
}