{{-- 
	@include('Form::base.datepicker', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
        'has_row'           => $item['has_row'],
        'class_col'         => $item['class_col'],
    ])
--}}

@if($class_col != '')
    <div class="{{ $class_col }}">
@endif
    <div class="mb-3 @if($has_row == true) row @endif">
        <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
        @if($has_row == true) 
            <div class="col-md-10">
        @endif
        <input type="text" class="form-control" autocomplete="off" name="{{ $name??'' }}" id="{{ $name??'' }}" placeholder="@lang($placeholder??$label??$name??'')" value="{{ old($name)??$value??'' }}">
        @if($has_row == true) 
            </div> 
        @endif
    </div>

@if($class_col != '')
    </div>
@endif

<script>
    $(document).ready(function() {
        $.datetimepicker.setLocale('{{ App::getLocale()??'vi' }}');
        $('#{{ $name??'' }}').datetimepicker({
            timepicker:false,
            format:'Y-m-d',
            scrollMonth : false,
            scrollInput : false,
        });
        @if ($required==1)
            validateInput('#{{ $name??'' }}', '@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
        @endif
    });
</script>