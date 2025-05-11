{{-- 
	@include('Form::base.password', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'placeholder' 		=> $item['placeholder'],
		'confirm' 			=> $item['confirm'],
        'has_row'           => $item['has_row'],
        'class_col'         => $item['class_col'],
    ])
--}}

@if ($has_row == true)
    @if($class_col != '')
        <div class="{{ $class_col }}">
    @endif
        <div class="mb-3 row">
            <label for="{{ $name??'' }}" class="col-md-2 col-form-label">@if($required==1) * @endif @lang($label??'')</label>
            <div class="col-md-10">
                <input type="password" class="form-control" autocomplete="off" name="{{ $name??'' }}" id="{{ $name??'' }}" placeholder="@lang($placeholder??$label??$name??'')" value="{{ old($name)??$value??'' }}">
            </div>
        </div>
    @if($class_col != '')
        </div>
    @endif
@else
    @if($class_col != '')
        <div class="{{ $class_col }}">
    @endif
        <div class="mb-3">
            <label for="{{ $name??'' }}">@if($required==1) * @endif @lang($label??'')</label>
            <input type="password" class="form-control" autocomplete="off" name="{{ $name??'' }}" id="{{ $name??'' }}" placeholder="@lang($placeholder??$label??$name??'')" value="{{ old($name)??$value??'' }}">
        </div>
    @if($class_col != '')
        </div>
    @endif
@endif
<script>
    $(document).ready(function() {
        {{-- Nếu bắt buộc --}}
        @if ($required==1)
            validateInput('#{{$name}}', '@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
        @endif
        {{-- Nếu có comfirm thì input hiện tại phải bằng input comfirm --}}
        @if (isset($confirm) && !empty($confirm))
            $('body').on('click', 'button[type=submit]', function(e) {
                value = $('#{{ $name??'' }}').val();
                confirm_value = $('#{{ $confirm??'' }}').val();
                if (!checkEmpty(value)) {
                    $('#{{ $name??'' }}').parent().find('.error').remove();
                    $('#{{ $name??'' }}').css('border', '1px solid #ced4da');
                    if (value != confirm_value) {
                        e.preventDefault();
                        $('#{{ $name??'' }}').parent().append(formHelper('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.is_comfirm')'));
                        openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.is_comfirm')');
                        $('#{{ $name??'' }}').css('border', '1px solid #ff0000');
                    }
                }
            });
        @endif
    });
</script>