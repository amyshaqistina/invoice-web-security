<x-filament-panels::page>
    <div>
        {{ $this->form }}

        <x-filament-actions::modals />
    </div>

    @script
        <script>
            $wire.on('reload-page', (e) => {
                //
                console.log(e)
                window.location.href = e[0];
            });
        </script>
    @endscript

</x-filament-panels::page>
