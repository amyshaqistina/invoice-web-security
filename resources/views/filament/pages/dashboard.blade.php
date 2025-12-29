<x-filament-panels::page class="p-4 bg-white rounded-lg shadow-md">
    <!-- Navigation Buttons for the Sections -->
    <div class="flex gap-4 mb-6">
        <button wire:click="tukarpage('overview')" class="btn btn-primary bg-orange-500 px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">
            Overview
        </button>
        <button wire:click="tukarpage('invoices')" class="btn btn-primary bg-orange-500 px-4 py-2 rounded-lg shadow hover:bg-green-600 transition">
            Recent Invoices
        </button>
        <button wire:click="tukarpage('payments')" class="btn btn-primary bg-orange-500 px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
            Recent Payments
        </button>
        <button wire:click="tukarpage('recurring-invoices')" class="btn btn-primary bg-orange-500 px-4 py-2 rounded-lg shadow hover:bg-purple-600 transition">
            Recurring Invoices
        </button>
    </div>

    <!-- Dynamically Render Content Based on the Selected Section -->
    <div class="content">
        @if($pageurl == 'overview')
            <div class="page-content">
                <h2 class="text-2xl font-semibold">Overview Chart</h2>
                <!-- Include the Overview Chart widget -->
                @livewire(\App\Filament\Widgets\StatsOverview::class)
            </div>
        @elseif($pageurl == 'invoices')
            <div class="page-content">
                <h2 class="text-2xl font-semibold">Recent Invoices</h2>
                <!-- Include the Recent Invoices Table widget -->
                 @livewire(\App\Filament\Widgets\RecentInvoicesTable::class)
            </div>
        @elseif($pageurl == 'payments')
            <div class="page-content">
                <h2 class="text-2xl font-semibold">Recent Payments</h2>
                <!-- Include the Recent Payments Table widget -->
                 @livewire(\App\Filament\Widgets\RecentPaymentTable::class)
            </div>
        @elseif($pageurl == 'recurring-invoices')
            <div class="page-content">
                <h2 class="text-2xl font-semibold">Recurring Invoices</h2>
                <!-- Include the Recurring Invoices Table widget -->
                @livewire(\App\Filament\Widgets\RecurringInvoiceTable::class)
            </div>
        @else
            <div class="page-content">
                <h2 class="text-2xl font-semibold">No Page Selected</h2>
                <p>Please select a page to display the content.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
