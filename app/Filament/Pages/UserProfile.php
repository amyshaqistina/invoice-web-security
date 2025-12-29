<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use App\Models\Post;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use Livewire\Component;

class UserProfile extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.user-profile';
    use InteractsWithForms;

    public ?array $data = [];

    // public function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             TextInput::make('Username')
    //                 ->required(),
    //             // ...
    //         ])
    //         ->statePath('data');
    // }

    public function mount(): void
    {
        $user = Auth::user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('company_address')
                            ->label('Company Address')
                            ->maxLength(255)
                            ->placeholder('Enter your company address'),

                        FileUpload::make('company_logo')
                            ->label('Company Logo')
                            ->image()
                            ->directory('logos')
                            ->visibility('public'),

                    ]),

                Section::make('Password')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->label('New Password')
                            ->minLength(8)
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                            ->required(fn($get) => filled($get('password'))),
                    ]),
            ])
            ->statePath('data');
    }
    public function create(): void
    {
        dd($this->form->getState());
    }

    public function update(): void
{
    $user = Auth::user();
    $formData = $this->form->getState();

    // Handle logo upload & resize
    if (isset($formData['company_logo'])) {
        $path = $formData['company_logo']; // Path like 'logos/xyz.png'

        $fullPath = storage_path('app/public/' . $path);

        // Resize using Intervention Image
        $image = Image::make($fullPath)
            ->resize(150, null, function ($constraint) {
                $constraint->aspectRatio();  // Keep proportions
                $constraint->upsize();       // Don't upscale small images
            });

        // Save resized image back to the same file
        $image->save($fullPath);

        $user->company_logo = $path;
    }

    // Update name and email
    $user->name = $formData['name'];
    $user->email = $formData['email'];
    $user->company_address = $formData['company_address'] ?? null;
    // Update password only if filled
    if (!empty($formData['password'])) {
        $user->password = $formData['password'];
    }

    $user->save();

    // Show success notification
    Notification::make()
        ->title('Profile updated successfully')
        ->success()
        ->send();
}

}

