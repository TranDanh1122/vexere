@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
    'icon' => null,
    'important' => false,
])

@php
    $color = match ($type) {
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
        default => 'alert-info',
    };

    $icon ??= match ($type) {
        'success' => '<i class="mdi mdi-check-all me-2"></i>',
        'danger' => '<i class="mdi mdi-block-helper me-2"></i>',
        'warning' => '<i class="mdi mdi-alert-outline me-2"></i>',
        default => '<i class="mdi mdi-alert-circle-outline me-2"></i>',
    };
@endphp

<div
    role="alert"
    {{ $attributes->class(['alert', $color, 'alert-dismissible' => $dismissible, 'alert-important' => $important]) }}
>
    @if ($icon)
        <div class="d-flex">
            <div class="d-flex" style="align-items: center">
                {!! $icon !!}
            </div>
            <div class="w-100">
    @endif

    @if ($title)
        <h4 @class(['alert-title' => !$important, 'mb-0'])>{!! $title !!}</h4>
    @endif

    {{ $slot }}

    @if ($icon)
        </div>
    </div>
@endif

@if ($dismissible)
    <a
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="close"
    ></a>
@endif

{{ $additional ?? '' }}
</div>
