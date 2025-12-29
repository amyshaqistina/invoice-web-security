<?php

namespace App\Filament\Resources;

use Filament\Panel;
use Z3d0X\FilamentLogger\Resources\ActivityResource;

class ModActivityResource extends ActivityResource
{
 
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return 'Maintenance';
    }

    public static function canViewAny(): bool
    {
          if(auth()->user()->hasRole('superadmin')){
            return true;
        }
        return false;
    }


}
