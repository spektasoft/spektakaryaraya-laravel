@php
    $partner = $getRecord();
@endphp

{{-- Card Container --}}
<div
    {{ $attributes->merge($getExtraAttributes())->class(['flex flex-col p-4 space-y-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-white/5 transition duration-300 -m-4']) }}>

    {{-- Header: Actions --}}
    <div class="flex flex-row justify-end items-start">
        <livewire:home.partners.partner-actions :class="'-mt-4 -mr-4'" :partner="$partner" />
    </div>

    {{-- Logo (Square) --}}
    <div
        class="w-full aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <a href="{{ route('partners.show', $partner) }}" wire:navigate class="block w-full h-full">
            <img src="{{ $partner->logo->url }}" alt="{{ $partner->name }}"
                class="object-cover w-full h-full transition duration-500 hover:scale-105">
        </a>
    </div>

    {{-- Title --}}
    <a href="{{ route('partners.show', $partner) }}" wire:navigate class="block group text-center">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition line-clamp-2"
            title="{{ $partner->name }}">
            {{ $partner->name }}
        </h3>
    </a>

</div>
