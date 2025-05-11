@props(['src', 'alt' => trans('Core::base.preview_image')])

<img
    {{ $attributes }}
    src="{{ $src }}"
    alt="{{ $alt }}"
/>
