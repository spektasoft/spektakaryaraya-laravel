@php
    $project = $getRecord();
@endphp

{{-- Card Container --}}
<div
    {{ $attributes->merge($getExtraAttributes())->class(['flex flex-col p-4 space-y-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:bg-gray-50 dark:hover:bg-white/5 transition duration-300 -m-4']) }}>

    {{-- Header: Year & Actions --}}
    <div class="flex flex-row justify-between items-start">
        <x-filament::badge :color="$project->status === 'published' ? 'primary' : 'gray'">
            {{ $project->start_date?->format('Y') ?? 'N/A' }}
        </x-filament::badge>

        <livewire:home.projects.project-actions :class="'-mt-4 -mr-4'" :project="$project" />
    </div>

    {{-- Title --}}
    <a href="{{ route('projects.show', $project) }}" wire:navigate class="block group">
        <div class="flex items-center gap-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition line-clamp-2"
                title="{{ $project->name }}">
                {{ $project->name }}
            </h3>
            @if ($project->status === 'archived')
                <x-filament::badge color="gray" size="xs">
                    {{ __('Archived') }}
                </x-filament::badge>
            @endif
        </div>
    </a>

    {{-- Logo (Square) --}}
    <div
        class="w-full aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <a href="{{ route('projects.show', $project) }}" wire:navigate class="block w-full h-full">
            @if ($project->logo)
                <img src="{{ $project->logo->url }}" alt="{{ $project->name }}"
                    class="object-cover w-full h-full transition duration-500 hover:scale-105">
            @else
                <div class="flex items-center justify-center w-full h-full bg-gray-50 dark:bg-gray-800">
                    <x-filament::icon icon="heroicon-o-photo" class="w-12 h-12 text-gray-400 dark:text-gray-500" />
                </div>
            @endif
        </a>
    </div>

    {{-- Description --}}
    <div class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 leading-relaxed">
        {!! str($project->description)->sanitizeHtml()->words(50) !!}
    </div>

</div>
