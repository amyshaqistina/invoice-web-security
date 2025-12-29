<?php

namespace App\Filament\Widgets;

use App\Models\RecurringInvoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class RecurringInvoiceTable extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                RecurringInvoice::query()
                    ->with('customer') // Eager load customer relationship
                    ->latest() // Order by newest first
                    ->limit(5) // Limit to 5 records
            )
            ->columns([

                // Create table column Invoice #
                TextColumn::make('invoice_number')
                    ->label('Invoice #'),

                // Create table column Client
                TextColumn::make('customer.name')
                    ->label('Client')
                    ->sortable(),

                // Create table column Nect Invoice Date
                TextColumn::make('next_invoice_date')
                    ->label('Next Invoice Date')
                    ->color('#000000')
                    ->badge()
            ])
            ->paginated(false)
            ;
    }
}
