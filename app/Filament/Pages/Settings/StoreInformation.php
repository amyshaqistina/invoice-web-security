<?php

namespace App\Filament\Pages\Settings;

use App\Models\Team;
use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;
use Filament\Forms\Form;

use Filament\Pages\Page;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class StoreInformation extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Settings';
    protected static string $view = 'filament.pages.settings.store-information';
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $team = Filament::getTenant();
        $this->form->fill($team?->getAttributes());
    }

    public static function canAccess(): bool
    {
          if(!auth()->user()->hasRole('superadmin')){
            return true;
        }
        return false;
    }

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
                            ->unique(table: Team::class, ignorable: Filament::getTenant()),

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

                        \Filament\Forms\Components\Select::make('state')
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
                    ->footerActions([
                        \Filament\Forms\Components\Actions\Action::make('Save Changes')
                            ->action(function () {
                                $data = $this->form->getState();
                                $team = Team::find(Filament::getTenant()->id);
                                $team->update($data);

                                Notification::make()
                                    ->title(__('Successfully updated team information'))
                                    ->success()
                                    ->send();
                            }),
                    ])


            ])
            ->statePath('data');
    }
}
