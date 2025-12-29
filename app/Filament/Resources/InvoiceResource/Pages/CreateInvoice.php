<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use App\Models\Invoice;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();
        return $resource::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        // Use database transaction to prevent race conditions
        DB::transaction(function () use ($record, $data) {
        // Get the latest invoice number atomically
        $lastInvoice = Invoice::lockForUpdate()->orderBy('id', 'desc')->first();
        $record->invoice_number = ($lastInvoice ? (int)$lastInvoice->invoice_number : 0) + 1;

        $record->save();

        // Process items
        $syncData = [];
        foreach ($data['item_invoice'] ?? [] as $item) {
            if (!empty($item['id']) && !empty($item['pivot']['quantity'])) {
                $itemId = (int)$item['id'];
                $quantity = max(1, min(1000, (int)$item['pivot']['quantity'])); // Sanitize
                $syncData[$itemId] = ['quantity' => $quantity];
            }
        }

            $record->items()->sync($syncData);
        }   );

        return $record;
    }

    protected function associateRecordWithTenant(Model $record, Model $tenant): Model
    {
        $relationship = static::getResource()::getTenantRelationship($tenant);

        $temp = $record->toArray();


        $temp2 = $relationship->save($record);

        $syncData = [];
        foreach ($temp['item_invoice'] as $item) {
            $syncData[$item['id']] = [
                'quantity' => $item['pivot']['quantity']
            ];
        }


        $temp2->items()->sync($syncData);
        return $temp2;
    }
}
