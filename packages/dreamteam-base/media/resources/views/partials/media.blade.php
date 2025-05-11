<div
    class="modal modal-blur fade media-modal rv-media-modal"
    id="rv_media_modal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div
        class="modal-dialog modal-dialog-centered modal-full"
        role="document"
    >
        <div class="modal-content bb-loading">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('media::media.gallery') }}</h5>
                <x-Core::modal.close-button />
            </div>
            <div
                class="p-0 modal-body media-modal-body media-modal-loading"
                id="rv_media_body"
            >
                <x-Core::loading />
            </div>
        </div>
    </div>
</div>

<x-Core::modal
    id="image-picker-add-from-url"
    :title="trans('media::media.add_from_url')"
    :form-action="route('media.download_url')"
    :form-attrs="['id' => 'image-picker-add-from-url-form']"
>
    <input type="hidden" name="image-box-target">
    <input type="hidden" name="image-allow-webp" value="no">
    <input type="hidden" name="image-allow-thumb" value="no">
    <input type="hidden" name="image-module-name" value="{{ $table_name ?? '' }}">

    <x-Core::form.text-input
        :label="trans('media::media.url')"
        type="url"
        name="url"
        placeholder="https://"
        :required="true"
    />
    <x-Core::form.checkbox
        :label="trans('media::media.real_path')"
        name="real_path"
        value="yes"
        :checked="true"
    />

    <x-slot:footer>
        <x-Core::button
            type="button"
            data-bs-dismiss="modal"
        >
            {{ trans('Core::forms.cancel') }}
        </x-Core::button>

        <x-Core::button
            type="submit"
            color="primary"
            data-bb-toggle="image-picker-add-from-url"
            form="image-picker-add-from-url-form"
        >
            {{ trans('Core::forms.save_and_continue') }}
        </x-Core::button>
    </x-slot:footer>
</x-Core::modal>

@include('media::config')

{!! apply_filters('core_base_media_after_assets', null) !!}
