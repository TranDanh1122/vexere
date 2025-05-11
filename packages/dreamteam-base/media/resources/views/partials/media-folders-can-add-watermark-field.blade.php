<div class="row">
    <div class="col-lg-12">
        <x-Core::form-group>
            <x-Core::form.label
                for="media_folders_can_add_watermark"
                :label="trans('media::media.setting.media_folders_can_add_watermark')"
            />
            <span style="display: block;font-size: 90%;margin-top: .25rem;">{{ trans('media::media.setting.all_helper_text') }}</span>
            <x-Core::form.fieldset class="mt-3" style="padding: 15px;border: 1px solid #ddd;border-radius: 10px;">
                <div class="multi-check-list-wrapper">
                    <x-Core::form-group>
                        <x-Core::form.checkbox
                            :label="trans('media::media.setting.all')"
                            class="check-all"
                            data-set=".media-folder"
                            name="media_folders_can_add_watermark_all"
                            :checked="empty($folderIds) || count($folderIds) === count($folders)"
                        >
                        </x-Core::form.checkbox>
                    </x-Core::form-group>

                    @foreach ($folders as $key => $item)
                        <x-Core::form-group @class(['mb-n3' => $loop->last])>
                            <x-Core::form.checkbox
                                :label="$item"
                                class="media-folder"
                                name="media_folders_can_add_watermark[]"
                                value="{{ $key }}"
                                id="media-folder-item-{{ $key }}"
                                    :checked="empty($folderIds) || in_array($key, $folderIds)"
                            />
                        </x-Core::form-group>
                    @endforeach
                </div>
            </x-Core::form.fieldset>
        </x-Core::form-group>

    </div>
</div>
