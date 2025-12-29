<div class="h-10 mt-20">
    <footer
        class="flex justify-between fixed bottom-0 left-0 z-20 w-full p-2 bg-white border-t border-gray-200 shadow md:flex md:items-center md:justify-between md:p-2 dark:bg-gray-800 dark:border-gray-600">
        <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }}
            <a href="#" class="hover:underline">{{ env('APP_NAME') }} Site™</a>. All Rights Reserved.
        </span>
        <div class="flex">

            <img alt="logo" src="{{ url('assets/logo.png') }}" style="width: 50px;"
                class="fi-logo justify-self-end p-1">
        </div>
    </footer>


    @script
        <script>
            // let currentPanel = @json(filament()->getCurrentPanel()->getId());
            // let existingPanel = localStorage.getItem('currentPanel');

            // if (currentPanel != existingPanel) {
            //     localStorage.setItem('currentPanel', currentPanel);
            //     location.reload();
            // }

            $wire.on('reload-page', (e) => {
                //
                window.location.href = e;
                console.log(e)
            });
        </script>
    @endscript

</div>
