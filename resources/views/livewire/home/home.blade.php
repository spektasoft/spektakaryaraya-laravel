<div class="flex flex-col min-h-screen">
    {{-- Hero Section --}}
    <section class="relative py-20 overflow-hidden bg-gray-100 dark:bg-gray-900 sm:py-32">
        <div class="absolute inset-0 bg-cover bg-center opacity-10 dark:opacity-20"
            style="{{ config('landing.hero_background_url') ? 'background-image: url(\'' . config('landing.hero_background_url') . '\');' : '' }}">
        </div>
        <div class="relative px-6 mx-auto max-w-7xl lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl">
                    {!! __('landing.hero.title') !!}
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    {{ __('landing.hero.description') }}
                </p>
                <div class="flex flex-col items-center gap-4 mt-10 sm:flex-row sm:justify-center">
                    <x-filament::button tag="a" href="https://wa.me/{{ config('services.whatsapp.number') }}"
                        target="_blank" size="xl" class="w-full sm:w-auto">
                        <div class="flex items-center gap-2">
                            <x-icons.whatsapp class="size-6 fill-current" />
                            {{ __('landing.hero.contact_us') }}
                            <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="size-3" />
                        </div>
                    </x-filament::button>
                    <x-filament::button tag="a" href="#projects" color="gray" size="xl"
                        icon="heroicon-m-arrow-long-right" icon-position="after" class="w-full sm:w-auto">
                        {{ __('landing.hero.learn_more') }}
                    </x-filament::button>
                </div>
            </div>
        </div>
    </section>

    {{-- Project Showcase --}}
    <section id="projects" class="py-24 bg-gray-50 dark:bg-gray-950 sm:py-32">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">
            <div class="max-w-2xl mx-auto text-center mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    {{ __('landing.projects.title') }}
                </h2>
                <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    {{ __('landing.projects.description') }}
                </p>
            </div>
            <livewire:home.projects.projects-table />
        </div>
    </section>

    {{-- Partner Showcase --}}
    <section class="py-24 bg-gray-100 dark:bg-gray-900/50 sm:py-32">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">
            <div class="max-w-2xl mx-auto text-center mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    {{ __('landing.partners.title') }}
                </h2>
                <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    {{ __('landing.partners.description') }}
                </p>
            </div>
            <livewire:home.partners.partners-table />
        </div>
    </section>

    {{-- CTA Section --}}
    <section id="contact" class="relative isolate overflow-hidden bg-gray-900/50 py-16 sm:py-24 lg:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-2">
                <div class="max-w-xl lg:max-w-lg">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        {{ __('landing.cta.title') }}
                    </h2>
                    <p class="mt-4 text-lg leading-8 text-gray-300">
                        {{ __('landing.cta.description') }}
                    </p>
                    <div class="mt-6 flex max-w-md gap-x-4">
                        <x-filament::button tag="a"
                            href="https://wa.me/{{ config('services.whatsapp.number') }}" target="_blank"
                            size="xl">
                            <div class="flex items-center gap-2">
                                <x-icons.whatsapp class="size-6 fill-current" />
                                {{ __('landing.cta.contact_us') }}
                                <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="size-3" />
                            </div>
                        </x-filament::button>
                    </div>
                </div>
                <dl class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:pt-2">
                    <div class="flex flex-col items-start">
                        <div class="rounded-md bg-white/5 p-2 ring-1 ring-white/10">
                            <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="h-6 w-6 text-white" />
                        </div>
                        <dt class="mt-4 font-semibold text-white">{{ __('landing.cta.free_consultation') }}</dt>
                        <dd class="mt-2 leading-7 text-gray-400">{{ __('landing.cta.free_consultation_desc') }}</dd>
                    </div>
                    <div class="flex flex-col items-start">
                        <div class="rounded-md bg-white/5 p-2 ring-1 ring-white/10">
                            <x-filament::icon icon="heroicon-o-hand-raised" class="h-6 w-6 text-white" />
                        </div>
                        <dt class="mt-4 font-semibold text-white">{{ __('landing.cta.no_spam') }}</dt>
                        <dd class="mt-2 leading-7 text-gray-400">{{ __('landing.cta.no_spam_desc') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>
</div>
