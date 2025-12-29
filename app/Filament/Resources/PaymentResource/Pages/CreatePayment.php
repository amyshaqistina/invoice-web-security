<?php

namespace App\Filament\Resources\PaymentResource\Pages;

// use App\Filament\Resources\PaymentResource;
// use Filament\Actions;
// use Filament\Resources\Pages\CreateRecord;

// class CreatePayment extends CreateRecord
// {
//     protected static string $resource = PaymentResource::class;

//     protected function getRedirectUrl(): string
//     {
//         $resource = static::getResource();
//         return $resource::getUrl('index');
//     }
// }

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PaymentResource;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Add logic if needed to handle toggles (e.g., send email)
        return $data;
    }

    protected function getFormSchema(): array
    {
        return [
            // Remove payment number field if it exists elsewhere

            Select::make('payment_mode')
                ->label('Payment Mode')
                ->options([
                    'bank transfer' => 'Bank Transfer',
                    'cash' => 'Cash',
                    'check' => 'Check',
                    'credit' => 'Credit',
                    'debit' => 'Debit',
                    'fpx' => 'FPX',
                    'master card' => 'Master Card',
                    'visa card' => 'Visa Card',
                    'tng' => 'TNG App',
                    'grab' => 'GRAB Pay',
                    'shopee' => 'Shopee Pay',
                    'bigpay' => 'BIG Pay',
                ])
                ->required(),

            TextInput::make('transaction_reference')
                ->label('Transaction Reference')
                ->required(),

            Toggle::make('send_email')
                ->label('Send Email'),

            Toggle::make('convert_currency')
                ->label('Convert Currency'),
        ];
    }
}

