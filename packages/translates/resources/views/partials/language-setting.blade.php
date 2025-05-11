@php
    $languages = Language::getActiveLanguage(['id', 'name', 'locale', 'flag']);
    $timezones = \DateTimeZone::listIdentifiers();
@endphp
<div class="custom">
    <form class="form-setting-language row" action="{{ route('admin.languages.settings') }}" method="post">
        @csrf
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="custom_type flex">
                <div class="mb-3">
                    <label for="default_language" class="">{{ __('Translate::language.default_language') }}</label>
                    <div class="select-search">
                        <select class="form-control w-100 language-selector" id="default_language" name="default_language">
                            @foreach ($languages as $key => $language)
                                <option value="{{ $language->locale }}"
                                    {{ ($data['default_language'] ?? 'vi') == $language->locale ? 'selected' : '' }}>
                                    {{ $language->name }}</label>
                            @endforeach
                        </select>
                    </div>
                    <p class="helper">{!! __('Translate::language.default_language_helper') !!}</p>
                </div>
                <div class="mb-3">
                    <label for="show_pannel_language"
                        class="">{{ __('Translate::language.show_pannel_language') }}</label>
                    <div class="select-search">
                        <select class="form-control w-100 language-selector" id="show_pannel_language" name="show_pannel_language">
                            @foreach ($languages as $key => $language)
                                <option value="{{ $language->locale }}"
                                    {{ ($data['show_pannel_language'] ?? 'vi') == $language->locale ? 'selected' : '' }}>
                                    {{ $language->name }}</label>
                            @endforeach
                        </select>
                    </div>
                    <p class="helper">{!! __('Translate::language.show_pannel_language_helper') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="mb-3 row">
                <label for="multiple_language"
                    class="col-md-3 col-form-label">{{ __('Translate::language.multiple_language') }}</label>
                <div class="col-md-9 form-switch form-switch-lg">
                    <input data-bs-toggle="tooltip"
                        data-bs-original-title="{{ trans('Translate::language.multiple_language') }}" type="checkbox"
                        class="form-check-input" name="multiple_language"
                        {{ ($data['multiple_language'] ?? 0) == 1 ? 'checked' : '' }} value="1"
                        style="margin-top: 6px;left: 0;">
                </div>
            </div>
            <div class="mb-3">
                <label for="timezone" class="">{{ __('Translate::language.timezone') }}</label>
                <div class="select-search">
                    <select class="form-control w-100" id="timezone" name="timezone">
                        @foreach ($timezones as $key => $timezone)
                            <option value="{{ $timezone }}"
                                {{ ($data['timezone'] ?? 'Asia/Ho_Chi_Minh') == $timezone ? 'selected' : '' }}>
                                {{ $timezone }}</label>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="">
            <x-Core::button type="submit" color="primary" id="setting-language-submit"
            data-store-url="{{ route('admin.languages.settings') }}">
            {{ trans('Translate::admin.update') }}
        </x-Core::button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('.select-search select').select2()
        $('.select-search > .select2').css({
            'width': '100%'
        })
    })
</script>
