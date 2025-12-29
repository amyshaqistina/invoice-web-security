<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center min-h-screen py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <div class="rounded-full bg-orange-100 p-6">
                        <svg class="h-12 w-12 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.502 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">500</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Internal Server Error</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    {{ $message ?? 'Something went wrong on our servers. We are working to fix the issue. Please try again later.' }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <x-filament::button
                        icon="heroicon-o-refresh"
                        color="primary"
                        wire:click="$refresh">
                        Reload Page
                    </x-filament::button>

                    <x-filament::button
                        icon="heroicon-o-home"
                        color="gray"
                        tag="a"
                        href="{{ filament()->getUrl() }}">
                        Go to Dashboard
                    </x-filament::button>
                </div>

                @if(config('app.debug') && isset($exception))
                <div class="mt-8 p-4 bg-red-50 rounded-lg text-left border border-red-200">
                    <details class="cursor-pointer" open>
                        <summary class="font-medium text-red-700">Error Details</summary>
                        <div class="mt-2 text-sm text-red-600 font-mono space-y-1">
                            <p><strong>Message:</strong> {{ $exception->getMessage() }}</p>
                            @if($exception->getFile())
                            <p><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
                            @endif
                            @if($exception->getCode())
                            <p><strong>Error Code:</strong> {{ $exception->getCode() }}</p>
                            @endif
                        </div>
                    </details>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
