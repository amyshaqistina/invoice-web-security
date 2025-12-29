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
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
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
                            ->displayFormat('d/m/Y H:i:s')
                            ->disabled()
                            ->dehydrated(),

                        DatePicker::make('updated_at')
                            ->label('Updated At')
                            ->displayFormat('d/m/Y H:i:s')
                            ->disabled()
                            ->dehydrated(),

                        DatePicker::make('deleted_at')
                            ->label('Deleted At')
                            ->displayFormat('d/m/Y H:i:s')
                            ->disabled()
                            ->dehydrated()
                    ])
                    ->columns(3)
                    ->hiddenOn('create'),
            ]);
    }


    protected function afterFill(): void
    {
        if ($this->record->attachment) {
            foreach ((array) $this->record->attachment as $file) {
                logger()->debug('Attachment URL: ' . Storage::disk('public')->url($file));
            }
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filament()->hasTenancy()) {
            $data['team_id'] = filament()->getTenant()->id;
        }

        return $data;
    }
}
