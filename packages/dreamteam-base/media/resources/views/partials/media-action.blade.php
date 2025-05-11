<div class="form-actions">
    <div class="form-actions__group">
        @method('put')
        <button type="submit" name="redirect" formaction="{{ route('admin.settings.media.update') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-save mr-1"></i>@lang('Translate::form.action.save')</button>
        <x-Core::button type="button" color="warning" class="generate-thumbnails-trigger-button btn-sm" :data-saving="trans('media::media.setting.generating_media_thumbnails')">
            {{ trans('media::media.setting.generate_thumbnails') }}
        </x-Core::button>
    </div>
</div>
