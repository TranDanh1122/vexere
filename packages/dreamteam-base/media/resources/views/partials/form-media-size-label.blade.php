<x-Core::form.label>
    {{ str_replace('-', ' ', Str::title(Str::slug($name))) }}
    <small>({{ trans('media::media.setting.default_size_value', ['size' => RvMedia::getConfig('sizes.' . $name)]) }})</small>
</x-Core::form.label>
