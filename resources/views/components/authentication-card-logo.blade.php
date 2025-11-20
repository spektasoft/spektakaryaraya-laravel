<a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
    <span class="flex items-center justify-center mb-1 rounded-md">
        <x-app-logo-icon icon-size="sm:text-5xl text-4xl" text-size="sm:text-4xl text-3xl" heading-visibility="block" />
    </span>
    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
</a>
