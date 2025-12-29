<?php

namespace App\Filament\Resources\RecurringInvoiceResource\Pages;

use Filament\Actions;
use App\Models\Invoice;
use Filament\Facades\Filament;
use App\Models\RecurringInvoice;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RecurringInvoiceResource;

class CreateRecurringInvoice extends CreateRecord
{
    protected static string $resource = RecurringInvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        $record->invoice_number = Invoice::orderBy('id', 'desc')->first()?->id + 1;

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }


        $temp = $record->toArray();
        $syncData = [];
        foreach ($temp['item_invoice'] as $item) {
            $syncData[$item['id']] = [
                'quantity' => $item['pivot']['quantity']
            ];
        }
        unset($record->item_invoice);

        $record->save();

        $record->items()->sync($syncData);
        return $record;
    }

    protected function associateRecordWithTenant(Model $record, Model $tenant): Model
    {
        $relationship = static::getResource()::getTenantRelationship($tenant);



        $temp2 = $relationship->save($record);








        RecurringInvoice::generate_invoice($temp2->id);

        return $temp2;
    }
}
