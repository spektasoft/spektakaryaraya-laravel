<x-app-layout>
    <div
        class="flex items-center justify-center w-full my-16 transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <footer class="w-full py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="https://wa.me/{{ config('services.whatsapp.number') }}" target="_blank" class="hover:underline">
                {{ __('navigation-menu.contact_us') }}
            </a>
        </footer>
    </div>
</x-app-layout>
