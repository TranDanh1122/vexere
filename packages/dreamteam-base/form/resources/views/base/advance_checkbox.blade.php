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
        <input type="checkbox" class="form-check-input" id="{{ $name??'' }}" name="{{ $name??'' }}" value="{{ $checked??0 }}" @if ($checked == $value) checked @endif style="margin-top: 6px;left: 0;">
        @if ($checked != $value)
            <input type="hidden" name="{{$name ?? ''}}" value="0">
        @endif
    </div>
</div>


<script>
    $(document).ready(function () {
        $('body').on('change', '#{{$name ?? ''}}', function(){
            if (this.checked) {
                $(this).parent().find('input[type="hidden"]').remove();
            } else {
                $(this).parent().append('<input type="hidden" name="{{ $name ?? '' }}" value="0">');
                // Fix lỗi thêm nhiều input hidden
                if($(this).parent().find('input[type="hidden"]').length > 1){
                    $(this).parent().find('input[type="hidden"]').slice(1).remove();
                }
            }
        });
    });
</script>
