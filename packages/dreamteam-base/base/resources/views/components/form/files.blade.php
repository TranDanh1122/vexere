@props(['name', 'allowThumb' => 'no', 'allowWebp' => 'no', 'attributes' => [], 'files' => [], 'addImagesLabel' => trans('Core::forms.add_files'), 'resetLabel' => trans('Core::forms.reset')])
@php
    $route = Route::current();
    $routeName = $route ? $route->getName() : '';
@endphp
<div {{ $attributes->merge(['class' => 'gallery-images-wrapper list-images form-fieldset']) }}>
    <div class="attachment-wrapper p-2">
        <div
            data-bb-toggle="gallery-add"
            @class([
                'text-center cursor-pointer default-placeholder-gallery-image',
                'hidden' => !empty($files),
            ])
            data-name="{{ $name }}"
            data-action="{{ $attributes['action'] ?? 'attachment' }}"
        >
            <div class="mb-3">
                <x-Core::icon
                    name="ti ti-photo-plus"
                    size="md"
                    class="text-secondary"
                />
            </div>
            <p class="mb-0 text-body">
                {{ trans('Core::base.click_here') }}
                {{ trans('Core::base.to_add_more_file') }}.
            </p>
        </div>
        <input
            name="{{ $name }}"
            type="hidden"
        >
        <div
            class="row w-100 list-gallery-media-images @if (empty($files)) hidden @endif"
            data-name="{{ $name }}"
            data-action="{{ $attributes['action'] ?? 'attachment' }}"
        >
            @if (!empty($files))
                @foreach ($files as $file)
                    @if (!empty($file))
                        <div class="col-lg-2 col-md-3 col-4 gallery-image-item-handler mb-2">
                            <div class="custom-image-box image-box">
                                <input
                                    class="attachment-url"
                                    name="{{ $name }}"
                                    type="hidden"
                                    value="{{ $file }}"
                                >
                                <div @class([
                                    'preview-image-wrapper w-100 mb-1'
                                ])>
                                    <div class="preview-image-inner" style="padding: 0;">
                                        <div class="attachment-info">
                                            <a href="{{ RvMedia::url($file) }}" target="_blank">{{ File::name($file) }}</a>
                                        </div>
                                        <div class="image-picker-backdrop"></div>

                                        <span class="image-picker-remove-button">
                                            <x-Core::button
                                                class="p-0"
                                                @style(['display: none' => empty($file)])
                                                :pill="true"
                                                data-bb-toggle="image-picker-remove"
                                                size="sm"
                                                icon="ti ti-x"
                                                :icon-only="true"
                                            >
                                                {{ __('Remove file') }}
                                            </x-Core::button>
                                        </span>
                                        <div
                                            data-bb-toggle="file-picker-edit"
                                            class="image-box-actions cursor-pointer"
                                        ></div>
                                    </div>
                                    <a class="preview-file" href="{{ RvMedia::url($file) }}" target="_blank">Preview</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    <div
        @style(['display: none' => empty($file)])
        class="footer-action p-2"
    >
        <a
            data-bb-toggle="gallery-add"
            href="#"
            class="me-2 cursor-pointer"
            data-action="{{ $attributes['action'] ?? 'attachment' }}"
        >{{ $addImagesLabel }}</a>
        <button
            class="text-danger cursor-pointer btn-link btn btn-sm btn-default"
            data-bb-toggle="gallery-reset"
        >
            {{ $resetLabel }}
        </button>
    </div>
</div>
