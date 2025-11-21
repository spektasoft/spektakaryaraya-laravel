<div class="min-h-screen bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    {{-- Hero Section --}}
    <div class="relative bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                <div class="lg:col-span-7">
                    <div class="flex items-center space-x-4 mb-6">
                        @if ($project->logo)
                            <img src="{{ $project->logo->url }}" alt="{{ $project->name }}"
                                class="h-16 w-16 object-contain rounded-xl shadow-md bg-white p-2">
                        @endif
                        <span
                            class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                            {{ $project->start_date->format('F Y') }}
                        </span>
                    </div>
                    <h1
                        class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                        {{ $project->name }}
                    </h1>
                    @if ($project->url)
                        <div class="mt-6">
                            <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out">
                                <x-heroicon-m-globe-alt class="h-5 w-5 mr-2" />
                                {{ __('Visit Project') }}
                            </a>
                        </div>
                    @endif
                </div>
                {{-- Decorative Element or Placeholder --}}
                <div class="hidden lg:block lg:col-span-5 mt-10 lg:mt-0">
                    <div
                        class="relative rounded-2xl overflow-hidden shadow-xl ring-1 ring-gray-900/10 bg-gray-100 dark:bg-gray-700 aspect-video flex items-center justify-center">
                        {{-- If there was a project banner image, it would go here. For now, using a stylized placeholder or the logo again large --}}
                        @if ($project->logo)
                            <img src="{{ $project->logo->url }}" alt="{{ $project->name }}"
                                class="h-32 w-auto opacity-50 grayscale">
                        @else
                            <x-heroicon-o-building-office-2 class="h-32 w-32 text-gray-300 dark:text-gray-600" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Description --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            {{ __('About the Project') }}
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 prose dark:prose-invert max-w-none">
                        {!! $project->description !!}
                    </div>
                </div>
            </div>

            {{-- Sidebar / Partners --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg sticky top-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            {{ __('Involved Partners') }}
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        {{ $this->table }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
