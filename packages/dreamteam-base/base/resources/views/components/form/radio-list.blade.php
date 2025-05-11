@props([
    'id' => null,
    'label' => null,
    'labelHelperText' => null,
    'name' => null,
    'value' => null,
    'options' => [],
    'helperText' => null,
    'wrapperClass' => null,
])

<x-Core::form-group :class="$wrapperClass">
    @if ($label)
        <x-Core::form.label
            :label="$label"
            :for="$id"
            :helperText="$labelHelperText"
        />
    @endif
    <div class="position-relative form-check-group">
        @foreach ($options as $key => $option)
            <x-Core::form.radio
                :name="$name"
                :value="$key"
                :checked="$key == $value"
                {{ $attributes }}
            >
                {{ $option }}
            </x-Core::form.radio>
        @endforeach
    </div>
    @if ($helperText)
        <x-Core::form.helper-text>{!! $helperText !!}</x-Core::form.helper-text>
    @endif
</x-Core::form-group>
