<div class="custom-image image-box image-box-image_{{ $keyImage }}" action="select-image" style="width: 100%;">
    <input type="hidden" class="image-data" name="{{ $customImageName }}" id="input-image_{{ $keyImage }}" value="{{ $image }}">
    <div class="preview-image-wrapper mb-1">
        <div class="preview-image-inner">
            <a data-bb-toggle="image-picker-choose" data-target="popup" class="image-box-actions" data-result="image_{{ $keyImage }}"
                data-action="select-image" data-allow-thumb="1" href="#">
                <img class="preview-image default-image"
                    data-default="/vendor/core/core/base/img/placeholder.png"
                    src="{{ RvMedia::getImageUrl($image) }}" alt="{{ trans('Core::base.preview_image') }}">
                <span class="image-picker-backdrop"></span>
            </a>
            <button class="btn btn-pill btn-icon  btn-sm image-picker-remove-button p-0"
                    type="button" data-bb-toggle="image-picker-remove"
                data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Remove image">
                <svg class="icon m-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M18 6l-12 12"></path>
                    <path d="M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    <a data-bb-toggle="image-picker-choose" data-target="popup" data-result="image_{{ $keyImage }}" data-action="select-image"
        data-allow-thumb="1" href="#">
        {{ trans('Core::forms.choose_image') }}
    </a>
    <div data-bb-toggle="upload-from-url">
        <span class="text-muted">{{ trans('media::media.or') }}</span>
        <a href="javascript:void(0)" class="mt-1" data-bs-toggle="modal" data-bs-target="#image-picker-add-from-url"
            data-bb-target=".image-box-image_{{ $keyImage }}">
            {{ trans('media::media.add_from_url') }}
        </a>
    </div>
</div>