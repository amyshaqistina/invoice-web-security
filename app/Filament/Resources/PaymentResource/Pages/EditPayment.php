<?php

namespace App\Filament\Resources\PaymentResource\Pages;

// use App\Filament\Resources\PaymentResource;
// use Filament\Actions;
// use Filament\Resources\Pages\EditRecord;

// class EditPayment extends EditRecord
// {
//     protected static string $resource = PaymentResource::class;

//     protected function getHeaderActions(): array
//     {
//         return [
//             Actions\DeleteAction::make(),
//             Actions\ForceDeleteAction::make(),
//             Actions\RestoreAction::make(),
//         ];
//     }

//     protected function getRedirectUrl(): string
//     {
//         $resource = static::getResource();
//         return $resource::getUrl('index');
//     }
// }

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getFormSchema(): array
{
    return [
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
