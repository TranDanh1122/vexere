@php
    $options = [
        1 => trans('Core::base.yes'),
        0 => trans('Core::base.no'),
    ];
@endphp

@include('Core::components.form.radio-list')
