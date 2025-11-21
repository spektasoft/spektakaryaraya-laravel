<div>
    <x-header :breadcrumbs="$this->getBreadcrumbs()" :actions="$this->getActions()">
        {{ $project->name }}
    </x-header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        {{ $this->infolist }}

        <x-filament::section>
            <x-slot name="heading">
                {{ trans_choice('partner.resource.model_label', 2) }}
            </x-slot>

            {{ $this->table }}
        </x-filament::section>
    </div>
</div>

