<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPaymentTable extends BaseWidget
{

    protected static ?string $pollingInterval = null;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->with('customer') // Eager load customer relationship
                    ->latest() // Order by newest first
                    ->limit(5) // Limit to 5 records

            )
            ->columns([

                // Create table column Payment #
                TextColumn::make('payment_number')
                    ->label('Payment #'),

                // Create table column Client
                TextColumn::make('customer.name')
                    ->label('Client')
                    ->sortable(),

                // Create table column Amount
                TextColumn::make('amount')
                    ->label('Amount')
                    ->color('info')
                    ->badge()

            ])
            ->paginated(false);
    }

    protected function getExtraAttributes(): array
    {
        return [
            'class' => 'shadow-2xl shadow-gray-500/10 dark:shadow-gray-800/10'
        ];
    }
}
