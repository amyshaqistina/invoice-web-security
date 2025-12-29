<x-filament-panels::page>
    <div>
        {{-- Show the uploaded logo if available --}}
        @if (auth()->user()->company_logo)
            <div class="flex justify-center mb-4">
                <img src="{{ Storage::url(auth()->user()->company_logo) }}"
                     alt="Company Logo"
                     class="h-16 rounded-md shadow" />
            </div>
        @endif

        {{-- Filament Form --}}
        <form wire:submit.prevent="update">
            {{ $this->form }}

            <div class="mt-8 flex justify-center">
                <x-filament::button
                    type="submit"
                    wire:target="update"
                    wire:loading.attr="disabled"
                    class="relative"
                >
                    {{-- Normal Button Text --}}
                    <span wire:loading.remove wire:target="update">
                        Save Profile
                    </span>

                    {{-- Loading Spinner --}}
                    <span wire:loading wire:target="update" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                        </svg>
                    </span>
                </x-filament::button>
            </div>
        </form>

        <x-filament-actions::modals />
    </div>
</x-filament-panels::page>
