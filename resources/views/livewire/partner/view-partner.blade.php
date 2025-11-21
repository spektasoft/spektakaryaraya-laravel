<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 lg:p-8">
            <div class="flex flex-col md:flex-row gap-8">
                <div class="w-full md:w-1/3">
                    <div
                        class="aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 flex items-center justify-center p-4">
                        <img src="{{ $partner->logo->url }}" alt="{{ $partner->name }}"
                            class="max-w-full max-h-full object-contain">
                    </div>
                </div>
                <div class="w-full md:w-2/3">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ $partner->name }}
                    </h1>

                    @if ($partner->url)
                        <a href="{{ $partner->url }}" target="_blank"
                            class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-500 mb-6">
                            <x-heroicon-m-globe-alt class="w-5 h-5" />
                            {{ $partner->url }}
                        </a>
                    @endif

                    <div class="prose dark:prose-invert max-w-none">
                        {!! str($partner->description)->sanitizeHtml() !!}
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    {{ __('Related Projects') }}
                </h2>
                {{ $this->table }}
            </div>
        </div>
    </div>
</div>
