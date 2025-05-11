@php
    $value = 1;
    $wrapper = $wrapper ?? true;
@endphp

@if($wrapper)
    <x-Core::form-group class="{{ $wrapperClass ?? null }}">
        <input type="hidden" name="{{ $name }}" value="0">

        @include('Core::components.form.checkbox')
    </x-Core::form-group>
@else
    <input type="hidden" name="{{ $name }}" value="0">

    @include('Core::components.form.checkbox')
@endif
