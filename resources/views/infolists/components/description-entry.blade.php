<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div x-data="{ expanded: false, isOverflowing: false }" x-init="$nextTick(() => { isOverflowing = $refs.content.scrollHeight > $refs.content.clientHeight })">
        <div x-ref="content" :class="expanded ? '' : 'line-clamp-6'" class="prose dark:prose-invert max-w-none">
            {!! $getState() !!}
        </div>
        <button x-show="!expanded && isOverflowing" @click="expanded = true"
            class="text-primary-600 hover:text-primary-500 mt-2 text-sm font-medium focus:outline-none"
            style="display: none;" x-show.important="!expanded && isOverflowing">
            {{ __('Read more') }}
        </button>
        <button x-show="expanded && isOverflowing" @click="expanded = false"
            class="text-primary-600 hover:text-primary-500 mt-2 text-sm font-medium focus:outline-none"
            style="display: none;" x-show.important="expanded && isOverflowing">
            {{ __('Read less') }}
        </button>
    </div>
</x-dynamic-component>
