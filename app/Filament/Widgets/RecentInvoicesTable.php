<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Invoice;

class RecentInvoicesTable extends BaseWidget
{
    protected static ?string $heading = 'Recent Invoices';
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = null;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->with('customer') // Load customer relationship
                    ->latest() // Order by newest first
                    ->limit(5) // Limit to 5 records
            )
            ->columns([

                // Create table column Invoice #
                TextColumn::make('invoice_number')
                    ->label('Invoice #'),

                // Create table column Customer
                TextColumn::make('customer.name')
                    ->label('Client')
                    ->sortable(),

                // Create table column status
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'info',
                        'cancel' => 'warning',
                        'overdue' => 'danger',
                        default => 'gray',
                    })

            ])
            ->paginated(false);

    }
}
