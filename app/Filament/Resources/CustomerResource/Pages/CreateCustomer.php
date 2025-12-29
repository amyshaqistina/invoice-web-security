<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Customer Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Customer Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('paid_to_date')
                            ->label('Paid to Date')
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),

                        TextInput::make('balance')
                            ->label('Balance')
                            ->numeric()
                            ->nullable()
                            ->columnSpan(1),

                    ])
                    ->columns(2),

                     Section::make('System Information')
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Created At')
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->hidden(),

                        DatePicker::make('updated_at')
                            ->label('Updated At')
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->hidden(),
                    ])
                    ->hidden(fn ($operation) => $operation !== 'create'),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (filament()->hasTenancy()) {
            $data['team_id'] = filament()->getTenant()->id;
        }

        return $data;
    }
}
