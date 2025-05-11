{{--
	@include('Form::base.checkbox', [
		'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'checked' 			=> $item['checked'],
		'label' 			=> $item['label'],
	])
--}}

@php
    if(!isset($inline)){
        $inline = true;
    }
@endphp

<div class="mb-3 row" style="position: relative;">
    <label for="{{ $name??'' }}" class="{{ $inline ? ($class_col == 'col-lg-12' ? 'col-md-2' : 'col-md-4') : ''}} col-form-label">@lang($label??'')</label>
    <div class="@if($class_col == 'col-lg-12') col-md-10 @else col-md-8 @endif form-switch form-switch-lg">
        <input type="checkbox" class="form-check-input" name="{{ $name??'' }}" value="{{ $checked??0 }}" @if ($checked == $value) checked @endif style="margin-top: 6px;left: 0;">
    </div>
</div>
