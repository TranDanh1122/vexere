{{--
    @include('Form::base.ckeditor', [
        'name'              => $item['name'],
        'value'             => $item['value'],
        'required'          => $item['required'],
        'label'             => $item['label'],
    ])
--}}
@if (isset($class_col) && $class_col != '')
    <div class="{{ $class_col }}">
@endif
<div class="mb-3 @if (isset($has_row) && $has_row == true) row @endif">
    <label for="{{ str_slug($name ?? '') }}" @if (isset($has_row) && $has_row == true) class="col-md-2 col-form-label" @endif>
        @if ($required == 1)
            *
        @endif @lang($label ?? '')
    </label>
    @if (isset($has_row) && $has_row == true)
        <div class="col-md-10">
    @endif
    <div class="form-group">
        <div class="editor_{{ str_slug($name) }}">
            <div class="d-flex mb-2">
                {!! apply_filters(BASE_FILTER_ADD_CUSTOM_BUTTON_EDITOR, null, $name, $item) !!}
                <div class="d-inline-block editor-action-item" style="margin-right: 15px;">
                    <x-Core::button type="button" class="btn_gallery btn-primary btn-sm" data-result="{{ $name }}"
                        data-multiple="true" data-action="media-insert-ckeditor">
                        <x-Core::icon name="ti ti-photo"/>
                        {{ trans('media::media.add') }}
                    </x-Core::button>
                </div>
                @if (!isset($notShortcode) || $notShortcode == false)
                    <div class="d-inline-block editor-action-item" style="margin-right: 15px;">
                        <x-Core::button data-bb-toggle="shortcode-list-modal" class="add_shortcode_btn_trigger btn-primary btn-sm"
                            data-result="{{ $name }}">
                            <x-Core::icon name="ti ti-box" />

                            {{ trans('Shortcode::shortcode.ui-blocks') }}
                        </x-Core::button>
                    </div>
                @endif
            </div>
            <textarea class="form-control form-control editor-ckeditor ays-ignore" placeholder="Mô tả ngắn" @if (!isset($notShortcode) || $notShortcode == false) with-short-code="" @endif
                v-pre="" id="{{ str_slug($name) }}" rows="4" name="{{ $name }}" cols="50">{!! htmlentities($value ?? '') !!}</textarea>
        </div>
    </div>
    @if (isset($has_row) && $has_row == true)
</div>
@endif
</div>
@if (isset($class_col) && $class_col != '')
    </div>
@endif
<script>
    $(document).ready(function() {
        window.siteEditorLocale = '{{ \Request()->setLanguage ?? \App::getLocale() }}'
        @if (!isset($notShortcode) || $notShortcode == false)
            @if ($required == 1)
                $('body').on('click', '.form-actions__group button[type=submit]', function(e) {
                    let content = window.parent.EDITOR.CKEDITOR['{{ $name }}'].getData();
                    if (checkEmpty(content)) {
                        e.preventDefault();
                        $('.editor_{{ $name }}').find('.error').remove();
                        $('.editor_{{ $name }}').append(formHelper(
                            '@lang($label ?? ($placeholder ?? ($name ?? ''))) @lang('Translate::form.valid.no_empty')'));
                        openPopup('@lang($label ?? ($placeholder ?? ($name ?? ''))) @lang('Translate::form.valid.no_empty')');
                    }
                });
            @endif
        @else
            @if ($required == 1)
                $('body').on('click', '.form-actions__group button[type=submit]', function(e) {
                    let content = window.parent.EDITOR.CKEDITOR['{{ str_slug($name) }}'].getData();
                    if (checkEmpty(content)) {
                        e.preventDefault();
                        $('.editor_{{ str_slug($name) }}').find('.error').remove();
                        $('.editor_{{ str_slug($name) }}').append(formHelper(
                            '@lang($label ?? ($placeholder ?? ($name ?? ''))) @lang('Translate::form.valid.no_empty')'));
                        openPopup('@lang($label ?? ($placeholder ?? ($name ?? ''))) @lang('Translate::form.valid.no_empty')');
                    }
                });
            @endif
        @endif
    })
</script>
