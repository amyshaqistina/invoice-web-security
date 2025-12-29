<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center min-h-screen py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <div class="rounded-full bg-red-100 p-6">
                        <svg class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">404</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <x-filament::button
                        icon="heroicon-o-arrow-left"
                        color="gray"
                        tag="a"
                        href="javascript:history.back()">
                        Go Back
                    </x-filament::button>

                    <x-filament::button
                        icon="heroicon-o-home"
                        tag="a"
                        href="{{ filament()->getUrl() }}">
                        Go to Dashboard
                    </x-filament::button>
                </div>

                @if(config('app.debug') && isset($exception))
                <div class="mt-8 p-4 bg-gray-50 rounded-lg text-left">
                    <details class="cursor-pointer">
                        <summary class="font-medium text-gray-700">Debug Information</summary>
                        <div class="mt-2 text-sm text-gray-600 font-mono">
                            <p><strong>Message:</strong> {{ $exception->getMessage() }}</p>
                            @if($exception->getFile())
                            <p><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
                            @endif
                        </div>
                    </details>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
