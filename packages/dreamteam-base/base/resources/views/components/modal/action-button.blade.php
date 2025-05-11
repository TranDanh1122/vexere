@props([
    'type' => 'info',
    'title' => null,
    'description' => null,
    'isActionModal' => 'false',
    'url' => null,
    'method' => null,
    'payload' => null,
    'confirmText' => trans('Core::base.yes'),
    'cancelText' => trans('Core::base.no'),
])

<div
    {{ $attributes->merge([
        'data-bb-toggle' => 'modal',
        'data-type' => $type,
        'data-action-modal' => $isActionModal,
        'data-url' => $url,
        'data-method' => $method,
        'data-payload' => json_encode($payload),
        'data-confirm-text' => $confirmText,
        'data-cancel-text' => $cancelText,
    ]) }}
    class="d-inline-block"
>
    <x-Core::button
        type="button"
        :color="$type"
    >
        {{ $slot }}
    </x-Core::button>

    @if ($title)
        <div class="modal-replace-title d-none">
            {{ $title }}
        </div>
    @endif

    @if ($description)
        <div class="modal-replace-description d-none">
            {{ $description }}
        </div>
    @endif
</div>

<x-Core::modal.push-once :type="$type" />
