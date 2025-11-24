<a href="https://wa.me/{{ config('services.whatsapp.number') }}" target="_blank"
    class="flex flex-row items-center justify-center gap-2 text-sm shrink-0 p-2.5 hover:bg-gray-500/10 rounded-2xl hover:dark:bg-gray-400/10">
    <x-icons.whatsapp class="size-6 fill-current text-gray-500 dark:text-gray-400" />
    <div class="hidden text-gray-500 sm:flex dark:text-gray-400">
        {{ __('navigation-menu.contact_us') }}
    </div>
    <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" color="gray" class="size-3" />
</a>
