{{-- 
	@include('Form::base.number', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'placeholder' 		=> $item['placeholder'],
        'has_row'           => $item['has_row'],
        'class_col'         => $item['class_col'],
        'disable'           => $item['disable'],
        'convert_number'    => $item['convert_number'],
    ])
--}}

@if($class_col != '')
    <div class="{{ $class_col }}">
@endif
    <div class="mb-3 @if($has_row == true) row @endif" style="position: relative;">
        <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
        @if($has_row == true) 
            <div class="col-md-10">
        @endif
        <input style="{{ $style ?? '' }}" @if($convert_number == true) type="text" @else type="number" @endif class="form-control input-mask-number" autocomplete="off" name="{{ $name??'' }}" id="{{ $name??'' }}" placeholder="@lang($placeholder??$label??$name??'')" value="{{ old($name)??$value??'' }}" @if($disable == true) disabled @endif>
        @if($has_row == true) 
            </div>
        @endif
    </div>

@if($class_col != '')
    </div>
@endif
<script>
    $(document).ready(function() {
        @if ($required==1)
            validateInput('#{{ $name??'' }}', '@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
        @endif
        @if($convert_number == true)
            convertStringToNumber('#{{ $name??'' }}');
            $('input[name="{{ $name??'' }}"]').on('keyup', function(){
                var number = $(this).val();
                number = format_price(number, 0, ',', ',');
                $(this).val(number);
            });
        @endif
    });
</script>
