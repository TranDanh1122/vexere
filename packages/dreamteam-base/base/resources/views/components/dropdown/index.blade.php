@props(['label', 'icon' => null, 'items' => null, 'hasArrow' => false, 'wrapperClass' => null, 'trigger' => null, 'position' => null])

<div @class(['dropdown', $wrapperClass])>
    @if ($trigger)
        {!! $trigger !!}
    @else
        <x-Core::button
            {{ $attributes->merge([
                'type' => 'button',
                'class' => 'dropdown-toggle',
                'data-bs-toggle' => 'dropdown',
            ]) }}
        >
            @if ($icon)
                <x-Core::icon :name="$icon" />
            @endif

            {{ $label }}
        </x-Core::button>
    @endif

    <div @class([
        'dropdown-menu',
        'dropdown-menu-arrow' => $hasArrow,
        "dropdown-menu-$position" => $position,
    ])>
        {{ $items ?? $slot }}
    </div>
</div>
