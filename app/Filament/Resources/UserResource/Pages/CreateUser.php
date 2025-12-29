<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\Team;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        $record =  User::updateOrCreate([
            'username' => $data['username'],
        ], [
            'id' => $data['id'],
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make('U53r_4cc0un7'),
        ]);

        // $team = Team::find(filament()->getTenant()->id);
        // $team->members()->syncWithoutDetaching([$record->id]);

        $record->save();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl;
        // return $this->getResource()::getUrl('index');
    }
    
}
