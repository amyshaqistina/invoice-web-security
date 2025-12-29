<?php

namespace App\Filament\Resources\PaymentResource\Pages;

// use App\Filament\Resources\PaymentResource;
// use Filament\Actions;
// use Filament\Resources\Pages\ListRecords;

// class ListPayments extends ListRecords
// {
//     protected static string $resource = PaymentResource::class;

//     protected function getHeaderActions(): array
//     {
//         return [
//             Actions\CreateAction::make(),
//         ];
//     }
// }

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Import CSV')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    FileUpload::make('file')
                        ->label('CSV File')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->required()
                        ->preserveFilenames()
                        ->disk('local')
                        ->directory('imports'),
                ])
                ->modalHeading('Import CSV File')
                ->modalSubmitActionLabel('Upload your payments data using a .csv file.')
                ->action(function (array $data): void {
                    $file = $data['file'];
                    $path = Storage::disk('local')->path($file);
                    Storage::disk('local')->delete($file);
                    // Handle file upload
                    // Import logic goes here
                }),

            Actions\CreateAction::make(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('record_status')
                ->label('Record Status')
                ->options([
                    'active' => 'Active',
                    'archived' => 'Archived',
                    'deleted' => 'Deleted',
                ]),

            SelectFilter::make('payment_status')
                ->label('Payment Status')
                ->options([
                    'pending' => 'Pending',
                    'cancelled' => 'Cancelled',
                    'failed' => 'Failed',
                    'completed' => 'Completed',
                    'refunded' => 'Refunded',
                    'partially_refunded' => 'Partially Refunded',
                    'partially_unapplied' => 'Partially Unapplied',
                ]),
        ];
    }
    protected function getTableActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        // Add other row actions here
    ];
    }
}


