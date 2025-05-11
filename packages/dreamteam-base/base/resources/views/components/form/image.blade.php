@props([
    'name',
    'allowThumb' => 'no',
    'allowWebp' => 'no',
    'value',
    'defaultImage' => RvMedia::getDefaultImage(),
    'allowAddFromUrl' => $isInAdmin = is_in_admin(true) && auth()->guard('admin')->check(),
])

@php
    $value = BaseHelper::stringify($value);
    $route = Route::current();
    $routeName = $route ? $route->getName() : '';
@endphp

<div {{ $attributes->merge(['class' => "image-box image-box-$name"]) }}>
    <input
        class="image-data"
        name="{{ $name }}"
        type="hidden"
        value="{{ $value }}"
        {{ $attributes->except('action') }}
    />

    @if (! $isInAdmin)
        <input
            class="media-image-input"
            type="file"
            style="display: none;"
            @if ($name) name="{{ $name }}_input" @endif
            @if (!isset($attributes['action']) || $attributes['action'] == 'select-image') accept="image/*" @endif
            {{ $attributes->except('action') }}
        />
    @endif

    <div
        style="width: 8rem"
        @class([
            'preview-image-wrapper mb-1',
            'preview-image-wrapper-not-allow-thumb' => ($allowThumb == 'yes' ? true : false),
        ])
    >
        <div class="preview-image-inner">
            <a
                data-bb-toggle="image-picker-choose"
                @if ($isInAdmin) data-target="popup" @else data-target="direct" @endif
                class="image-box-actions"
                data-result="{{ $name }}"
                data-action="{{ $attributes['action'] ?? 'select-image' }}"
                data-allow-thumb="{{ $allowThumb }}"
                data-allow-webp="{{ $allowWebp }}"
                data-module-name="{{ $table_name ?? '' }}"
                href="#"
            >
                <x-Core::image
                    @class(['preview-image', 'default-image' => !$value])
                    data-default="{{ $defaultImage = $defaultImage ?: RvMedia::getDefaultImage() }}"
                    src="{{ RvMedia::getImageUrl($value, null, null, false, $defaultImage) }}"
                    alt="{{ trans('Core::base.preview_image') }}"
                />
                <span class="image-picker-backdrop"></span>
            </a>
            <x-Core::button
                @style(['display: none' => empty($value), '--bb-btn-font-size: 0.5rem'])
                class="image-picker-remove-button p-0"
                :pill="true"
                data-bb-toggle="image-picker-remove"
                size="sm"
                icon="ti ti-x"
                :icon-only="true"
                :tooltip="trans('Core::forms.remove_image')"
            />
        </div>
    </div>

    <a
        data-bb-toggle="image-picker-choose"
        @if ($isInAdmin) data-target="popup" @else data-target="direct" @endif
        data-result="{{ $name }}"
        data-action="{{ $attributes['action'] ?? 'select-image' }}"
        data-allow-thumb="{{ $allowThumb }}"
        data-allow-webp="{{ $allowWebp }}"
        data-module-name="{{ $table_name ?? '' }}"
        href="#"
    >
        {{ trans('Core::forms.choose_image') }}
    </a>
    <br>
    <span class="text-muted">{{ trans('media::media.or') }}</span>
    <a href="javascript:void(0)" class="mt-1 btn-upload-from-device" data-bb-toggle="btn-upload-from-device">
        {{ trans('media::media.add_from_device') }}
    </a>
    <input type="file" class="input-upload-from-device" style="display: none" multiple="" />

    @if($allowAddFromUrl)
        <div data-bb-toggle="upload-from-url">
            <span class="text-muted">{{ trans('media::media.or') }}</span>
            <a
                href="javascript:void(0)"
                class="mt-1"
                data-bs-toggle="modal"
                data-bs-target="#image-picker-add-from-url"
                data-bb-target=".image-box-{{ $name }}"
                data-allow-thumb="{{ $allowThumb }}"
                data-allow-webp="{{ $allowWebp }}"
                data-module-name="{{ $table_name ?? '' }}"
            >
                {{ trans('media::media.add_from_url') }}
            </a>
        </div>
    @endif
</div>
