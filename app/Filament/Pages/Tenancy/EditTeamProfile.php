<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditTeamProfile extends EditTenantProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.tenancy.edit-team-profile';


    public static function getLabel(): string
    {
        return 'Team profile';
    }

    // public static function canAccess(): bool
    // {
    //     return false;
    // }

    // public static function canView(Model $tenant): bool
    // {
    //    return false;
    // }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('')
                    ->id('team-profile')
                    ->schema([
                        TextInput::make('name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->live()
                            ->hint('Choose your URL address')
                            ->helperText(fn($get) => url('/') . '/' . filament()->getCurrentPanel()->getPath() . '/' . $get('slug'))
                            ->required()
                            ->unique(table: Team::class, ignoreRecord: true),

                        TextInput::make('unit_house_no')
                            ->label('Unit/House No')
                            ->maxLength(255)
                            ->nullable(),

                        TextInput::make('address_1')
                            ->label('Address 1')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('address_2')
                            ->label('Address 2')
                            ->maxLength(255)
                            ->nullable(),

                        TextInput::make('city')
                            ->label('City')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->maxLength(10)
                            ->required(),

                        Select::make('state')
                            ->label('State')
                            ->options([
                                'Johor' => 'Johor',
                                'Kedah' => 'Kedah',
                                'Kelantan' => 'Kelantan',
                                'Melaka' => 'Melaka',
                                'Negeri Sembilan' => 'Negeri Sembilan',
                                'Pahang' => 'Pahang',
                                'Perak' => 'Perak',
                                'Perlis' => 'Perlis',
                                'Pulau Pinang' => 'Pulau Pinang',
                                'Sabah' => 'Sabah',
                                'Sarawak' => 'Sarawak',
                                'Selangor' => 'Selangor',
                                'Terengganu' => 'Terengganu',
                                'Wilayah Persekutuan Kuala Lumpur' => 'Wilayah Persekutuan Kuala Lumpur',
                                'Wilayah Persekutuan Labuan' => 'Wilayah Persekutuan Labuan',
                                'Wilayah Persekutuan Putrajaya' => 'Wilayah Persekutuan Putrajaya',
                            ])
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2)


            ]);
    }
}
