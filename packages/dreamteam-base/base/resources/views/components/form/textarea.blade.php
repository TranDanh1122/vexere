@props([
    'id' => null,
    'label' => null,
    'name' => null,
    'value' => old($name),
    'helperText' => null,
    'errorKey' => $name,
])

@php
    $id = $attributes->get('id', $name) ?? Str::random(8);

    $classes = Arr::toCssClasses(['form-control', 'is-invalid' => $errors->has($errorKey)]);
@endphp

<x-Core::form-group>
    @if ($label)
        <x-Core::form.label
            :label="$label"
            :for="$id"
        />
    @endif

    <textarea {{ $attributes->merge(['name' => $name, 'id' => $id])->class($classes) }}>{{ $value ?: $slot }}</textarea>

    @if ($helperText)
        <x-Core::form.helper-text>{!! $helperText !!}</x-Core::form.helper-text>
    @endif

    <x-Core::form.error :key="$errorKey" />
</x-Core::form-group>
