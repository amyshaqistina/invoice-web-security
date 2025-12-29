<?php

namespace App\Filament\Guest\Pages;

use Livewire\Component;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class Home extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guest.pages.home';
    protected static ?string $slug = '/';
    protected static bool $shouldRegisterNavigation = false;



    public function getTitle(): string | Htmlable
    {
        return __('');
    }

    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->id('choose-login')
                ->schema([
                    TextInput::make('domain')
                    ->prefix(fn()=> url('/').'/')
                    ->required()
                ])
                ->footerActions([
                    \Filament\Forms\Components\Actions\Action::make('login')
                        ->action(function ($livewire) {
                            $livewire->redirect(url('/'.$this->data['domain'].'/login'), navigate:true);
                            

                        }),
                ])
                ->footerActionsAlignment(Alignment::End),
               
            ])
            ->statePath('data');
    }
}
