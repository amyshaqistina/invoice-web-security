<?php

namespace App\Filament\Resources\RecurringInvoiceResource\Pages;

use Filament\Actions;
use App\Filament\Resources\ItemResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RecurringInvoiceResource;

class ListRecurringInvoices extends ListRecords
{
    protected static string $resource = RecurringInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Items')
             ->url(ItemResource::getUrl('index')),
        ];
    }
}
