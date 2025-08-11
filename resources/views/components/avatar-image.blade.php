@props([
    'src' => null,
    'alt' => '',
    'size' => '10',
    'name' => null,
    'nameClass' => 'text-base font-medium text-gray-900 dark:text-gray-100'
])

<div class="flex items-center space-x-3">
    @if ($src)
        <img src="{{ $src }}"
             alt="{{ $alt }}"
             {{ $attributes->merge(['class' => "w-{$size} h-{$size} object-cover rounded-full"]) }}>
    @else
        <div {{ $attributes->merge(['class' => "w-{$size} h-{$size} rounded-full bg-gray-200 flex items-center justify-center"]) }}>
            <span class="text-gray-500 text-{{ (int)($size/3) }}xl uppercase">
                {{ Str::substr($alt, 0, 1) }}
            </span>
        </div>
    @endif

    @if($name)
        <div class="{{ $nameClass }}">
            {{ $name }}
        </div>
    @endif
</div> 