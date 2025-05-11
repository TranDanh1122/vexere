<x-Core::modal.action
    id="generate-thumbnails-modal"
    :title="trans('media::media.setting.generate_thumbnails')"
    :description="trans('media::media.setting.generate_thumbnails_description')"
    type="warning"
    :submit-button-label="trans('media::media.setting.generate')"
    :submit-button-attrs="['id' => 'generate-thumbnails-button']"
    :has-form="true"
    :form-action="route('admin.settings.media.generate-thumbnails')"
/>