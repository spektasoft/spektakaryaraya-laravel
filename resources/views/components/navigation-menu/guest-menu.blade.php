<x-filament::dropdown placement="bottom-end" teleport>
    <x-slot name="trigger">
        <div class="p-2.5 hover:bg-gray-500/10 rounded-2xl hover:dark:bg-gray-400/10">
            <x-filament::icon-button color="gray" icon="heroicon-m-ellipsis-vertical" :label="__('navigation-menu.menu.open_menu')" size="xl" />
        </div>
    </x-slot>

    <x-filament::dropdown.header :icon="'heroicon-o-paint-brush'">
        {{ __('navigation-menu.menu.theme') }}
    </x-filament::dropdown.header>

    <x-filament::dropdown.list>
        <x-filament-panels::theme-switcher />
    </x-filament::dropdown.list>

</x-filament::dropdown>
