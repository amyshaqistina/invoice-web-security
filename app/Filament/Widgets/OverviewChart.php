<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;


class OverviewChart extends ChartWidget
{
    protected static ?string $heading = 'Payment Trends';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = null;
    protected function getData(): array
    {


        $payments = Payment::query()
        ->selectRaw('YEAR(date) as year, SUM(amount) as total')
        ->groupBy('year')
        ->orderBy('year')
        ->get();

        $totals = $payments->pluck('total')->map(fn($total) => (float) $total);
        $years = $payments->pluck('year');

        return [
            'datasets' => [
            [
                'label' => 'Payments Received',
                'data' => $totals,
                'borderColor' => '#3b12f6',
                'backgroundColor' => '#c7d2fe',
            ],
        ],
            'labels' => $years,
        ];

    }

    protected function getType(): string
    {
        return 'line';
    }

}
