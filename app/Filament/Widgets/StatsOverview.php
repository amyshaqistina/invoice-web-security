<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Invoice;
use App\Models\RecurringInvoice;
use App\Models\Payment;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected function getStats(): array
    {
        // Total invoices sent
        $sentInvoices = Invoice::where('status','sent')->count();

        // Total invoices pending
        $pendingInvoices = Invoice::where('status','pending')->count();

        // Total invoices overdue
        $overdueInvoices = Invoice::where('status','overdue')->count();

        $totalReceived = Payment::sum('amount');

        return [

            // Widgets for Total Recieved
            Stat::make('Total Received','RM'.number_format($totalReceived,2))

            ->description('Revenue (Demo Data)')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            ->chart([7, 3, 4, 5, 6, 3, 5, 3])
            ->extraAttributes(['class' => 'border-2 border-dashed border-success-500']),

            // Widgets for Invoice Sent
            Stat::make('Invoices Sent', $sentInvoices . ' Invoices')

            ->description('Total Invoices Sent')
            ->color('info')
            ->extraAttributes(['class' => 'border-2 border border-info-500']),


            // Widgets for Pending
            Stat::make('Pending', $pendingInvoices . ' Invoices')
            ->description('This Month')
            ->color('warning')
            ->extraAttributes(['class' => 'border-2 border border-warning-500']),

            // Widgets for Overdue
            Stat::make('Overdue', $overdueInvoices . ' Invoices')
            ->description('Pending')
            ->color('danger')
            ->chart([7, 3, 4, 5, 6, 3, 5, 3])
            ->extraAttributes(['class' => 'border-2 border-dashed border-danger-500']),

        ];
    }
}
