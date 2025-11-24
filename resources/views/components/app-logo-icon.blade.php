@props(['iconSize' => 'text-3xl', 'textSize' => 'text-2xl', 'headingVisibility' => 'hidden sm:block'])

<div {{ $attributes->merge(['class' => 'flex flex-row gap-2 items-center']) }}>
    <span class="font-logo text-primary-500 {{ $iconSize }}">
        S
    </span>
    <span
        class="font-heading {{ $textSize }} font-semibold whitespace-nowrap text-primary-500 {{ $headingVisibility }}">
        SPEKTA
    </span>
</div>
