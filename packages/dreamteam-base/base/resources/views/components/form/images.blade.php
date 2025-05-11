@props(['name', 'allowThumb' => 'no', 'allowWebp' => 'no', 'images' => [], 'addImagesLabel' => trans('Core::forms.add_images'), 'resetLabel' => trans('Core::forms.reset')])
@php
    $route = Route::current();
    $routeName = $route ? $route->getName() : '';
@endphp
<div {{ $attributes->merge(['class' => 'gallery-images-wrapper list-images form-fieldset']) }}>
    <div class="images-wrapper p-2">
        <div
            data-bb-toggle="gallery-add"
            @class([
                'text-center cursor-pointer default-placeholder-gallery-image',
                'hidden' => !empty($images),
            ])
            data-name="{{ $name }}"
            data-allow-webp="{{ $allowWebp }}"
            data-allow-thumb="{{ $allowThumb }}"
            data-module-name="{{ $table_name ?? '' }}"
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
                {{ trans('Core::base.to_add_more_image') }}.
            </p>
        </div>
        <input
            name="{{ $name }}"
            type="hidden"
        >
        <div
            class="row w-100 list-gallery-media-images @if (empty($images)) hidden @endif"
            data-name="{{ $name }}"
            data-allow-thumb="{{ $allowThumb }}"
            data-allow-webp="{{ $allowWebp }}"
            data-module-name="{{ $table_name ?? '' }}"
        >
            @if (!empty($images))
                @foreach ($images as $image)
                    @if (!empty($image))
                        <div class="col-lg-2 col-md-3 col-4 gallery-image-item-handler mb-2">
                            <div class="custom-image-box image-box">
                                <input
                                    class="image-data"
                                    name="{{ $name }}"
                                    type="hidden"
                                    value="{{ $image }}"
                                >
                                <div @class([
                                    'preview-image-wrapper w-100 mb-1',
                                    'preview-image-wrapper-not-allow-thumb' => ($allowThumb == 'yes' ? true : false),
                                ])>
                                    <div class="preview-image-inner">
                                        <x-Core::image
                                            class="preview-image"
                                            data-default="{{ $defaultImage = RvMedia::getDefaultImage() }}"
                                            src="{{ RvMedia::getImageUrl($image, null, null, false, $defaultImage) }}"
                                        />
                                        <div class="image-picker-backdrop"></div>

                                        <span class="image-picker-remove-button">
                                            <x-Core::button
                                                class="p-0"
                                                @style(['display: none' => empty($image)])
                                                :pill="true"
                                                data-bb-toggle="image-picker-remove"
                                                size="sm"
                                                icon="ti ti-x"
                                                :icon-only="true"
                                            >
                                                {{ __('Remove image') }}
                                            </x-Core::button>
                                        </span>
                                        <div
                                            data-bb-toggle="image-picker-edit"
                                            class="image-box-actions cursor-pointer"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    <div
        @style(['display: none' => empty($image)])
        class="footer-action p-2"
    >
        <a
            data-bb-toggle="gallery-add"
            href="#"
            class="me-2 cursor-pointer"
            data-allow-webp="{{ $allowWebp }}"
            data-allow-thumb="{{ $allowThumb }}"
            data-module-name="{{ $table_name ?? '' }}"
        >{{ $addImagesLabel }}</a>
        <button
            class="text-danger cursor-pointer btn-link btn btn-sm btn-default"
            data-bb-toggle="gallery-reset"
        >
            {{ $resetLabel }}
        </button>
    </div>
</div>
