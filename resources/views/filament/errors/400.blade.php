<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center min-h-screen py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <div class="rounded-full bg-blue-100 p-6">
                        <svg class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">400</h1>
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Bad Request</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    {{ $message ?? 'The server could not understand your request due to invalid syntax.' }}
                </p>

                <div class="space-y-4">
                    <p class="text-sm text-gray-500">This could be due to:</p>
                    <ul class="text-sm text-gray-600 text-left list-disc list-inside max-w-xs mx-auto">
                        <li>Invalid form data submission</li>
                        <li>Missing required parameters</li>
                        <li>Malformed request syntax</li>
                        <li>Invalid file upload</li>
                    </ul>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
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
                <div class="mt-8 p-4 bg-blue-50 rounded-lg text-left">
                    <details class="cursor-pointer">
                        <summary class="font-medium text-blue-700">Request Details</summary>
                        <div class="mt-2 text-sm text-blue-600 font-mono">
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
