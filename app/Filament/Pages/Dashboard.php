<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\OverviewChart;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Widgets\RecentPaymentTable;
use App\Filament\Widgets\RecentInvoicesTable;
use App\Filament\Widgets\RecurringInvoiceTable;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = '/';
    public $pageurl;


    function mount()
    {

        $this->pageurl = 'overview';
    }

    function tukarpage($name)
    {
        $this->pageurl = $name;
    }


    // protected function getFooterWidgets(): array
    // {
    //     return [
    //         OverviewChart::class,
    //         RecentInvoicesTable::class,
    //         RecentPaymentTable::class,
    //         RecurringInvoiceTable::class,
    //     ];
    // }

    public function getTitle(): string | Htmlable
    {
        return __('');
    }
}
