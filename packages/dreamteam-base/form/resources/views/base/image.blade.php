{{-- 
    @include('Form::base.image', [
        'name'              => $item['name'],
        'value'             => $item['value'],
        'required'          => $item['required'],
        'label'             => $item['label'],
        'title_btn'         => $item['title_btn'],
    ])

--}}
@php
    $attributes = $attributes ?? [];
    $options['wrapper'] = false;
    $showLabel = false;
    $showField = true;
    $showError = true;
    $nameKey = $name;
    if (($showLabel ?? '') && empty($options['label'] ?? '')) {
        $options['label'] = trans('Core::forms.image');
    }
@endphp

@if ($class_col != '')
    <div class="{{ $class_col }}">
@endif
<div class="mb-3 @if ($has_row == true) row @endif">
    <label for="{{ $name ?? '' }}" @if ($has_row == true) class="col-md-2 col-form-label" @endif>
        @if ($required == 1)
            *
        @endif @lang($label ?? '')
    </label>
    @if ($has_row == true)
        <div class="col-md-10">
    @endif
    <x-Core::form.field :showLabel="$showLabel" :showField="$showField" :options="$options" :name="$name" :prepend="$prepend ?? null"
        :append="$append ?? null" :showError="$showError" :nameKey="$nameKey">
        @php
            $allowThumb = Arr::get($attributes, 'allow_thumb', 'no');
            $allowWebp = Arr::get($attributes, 'allow_webp', 'no');
        @endphp

        <x-Core::form.image :allow-thumb="$allowThumb" :allow-webp="$allowWebp" :name="$name" :value="$value" action="select-image" />
    </x-Core::form.field>
    <p class="help-text" style="padding-top: 5px;font-size: 12px;">{!! $helper_text ?? '' !!}</p>
    @if ($has_row == true)
</div>
@endif
</div>

@if ($class_col != '')
    </div>
@endif
<div class="modal" id="modal-message-full-storage">
    <div class="modal-content" style="width: 500px;">
        <div class="modal-close"></div>
        <div class="">
            <div class="message" style="text-align: center;padding: 20px;"></div>
        </div>
    </div>
</div>
