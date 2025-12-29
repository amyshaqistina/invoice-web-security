<?php

namespace App\Filament\Guest\Pages;

use App\Models\Team;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class HomeTeam extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guest.pages.home-team';
    protected static ?string $slug = '{team?}';
    protected static bool $shouldRegisterNavigation = false;
    public $team ;


    public function mount(){
    
        $check = Team::where('slug', $this->team)->first();
        if(!$check){
            $this->redirect(url('/guest'));
        }

    }

    public function getTitle(): string | Htmlable
    {
        return __('');
    }
}
