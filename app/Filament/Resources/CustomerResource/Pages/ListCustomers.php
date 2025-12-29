<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Customer')  // #1 - More descriptive label
                ->icon('heroicon-o-plus')  // #7 - Added icon
                ->modalHeading('Create New Customer'),  // #8 - More descriptive modal heading
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array  // #3 - Custom pagination options
    {
        return [10, 25, 50, 100];
    }
}
