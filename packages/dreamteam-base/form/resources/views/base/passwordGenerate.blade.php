{{-- 
	@include('Form::base.passwordGenerate', [
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
        <div class="mb-3 row" style="position: relative;">
            <label for="{{ $name??'' }}" class="col-md-2 col-form-label">@if($required==1) * @endif @lang($label??'')</label>
            <div class="col-md-10">
                <input type="password" class="form-control" autocomplete="off" name="{{ $name??'' }}" id="{{ $name??'' }}" placeholder="@lang($placeholder??$label??$name??'')" value="{{ old($name)??$value??'' }}">
                <div class="strength">
                    <span class="strength-result">@lang('Translate::form.strength')</span>
                    <div class="strength-box">
                        <span class="strength-box__list"><div></div></span>
                        <span class="strength-box__list"><div></div></span>
                        <span class="strength-box__list"><div></div></span>
                        <span class="strength-box__list"><div></div></span>
                        <span class="strength-box__list"><div></div></span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2" style="padding-left: 0;position: absolute;right: 0;top: 27px;">
                    <button type="button" class="btn btn-primary" id="btn-genpassword-{{ $name??'' }}" style="width: 100%;">@lang('Translate::form.password_auto')</button>
                </div>
            </div>
        </div>
    @if($class_col != '')
        </div>
    @endif
@else
    @if($class_col != '')
        <div class="{{ $class_col }}">
    @endif
        <div class="mb-3" style="position: relative;">
            <label for="{{ $name??'' }}">@if($required==1) * @endif @lang($label??'')</label>
            <input type="password" class="form-control" autocomplete="off" name="{{ $name??'' }}" id="{{ $name??'' }}" placeholder="@lang($placeholder??$label??$name??'')" value="{{ old($name)??$value??'' }}">
            <div class="strength">
                <span class="strength-result">@lang('Translate::form.strength')</span>
                <div class="strength-box">
                    <span class="strength-box__list"><div></div></span>
                    <span class="strength-box__list"><div></div></span>
                    <span class="strength-box__list"><div></div></span>
                    <span class="strength-box__list"><div></div></span>
                    <span class="strength-box__list"><div></div></span>
                </div>
            </div>
            <div class="" style="padding-left: 0;position: absolute;right: 0;top: 27px;">
                <button type="button" class="btn btn-primary" id="btn-genpassword-{{ $name??'' }}" style="width: 100%;">@lang('Translate::form.password_auto')</button>
            </div>
        </div>
    @if($class_col != '')
        </div>
    @endif
@endif

<script>
    $(document).ready(function() {
        {{-- click vào button tạo tự động --}}
        $('body').on('click', '#btn-genpassword-{{ $name??'' }}', function() {
            password = passwordGenerator(10);
            if (prompt("@lang('Translate::form.password_generate')", password)) {
                $('#{{ $name??'' }}').val(password);
                $('#{{ $name??'' }}').change();
            }
            return false;
        });
        {{-- khi input thay đổi --}}
        $('body').on('change keyup', '#{{ $name??'' }}', function() {
            password = $(this).val();
            strength = passwordStrength(password);
            strength_class = $(this).closest('.mb-3').find('.strength');
            strength_result = $(this).closest('.mb-3').find('.strength-result');
            switch(strength) {
                case 1:
                    strength_class.removeClass().addClass('strength strength1');
                    strength_result.html('@lang('Translate::form.too_weak')');
                    break;
                case 2:
                    strength_class.removeClass().addClass('strength strength2');
                    strength_result.html('@lang('Translate::form.weak')');
                    break;
                case 3:
                    strength_class.removeClass().addClass('strength strength3');
                    strength_result.html('@lang('Translate::form.normal')');
                    break;
                case 4:
                    strength_class.removeClass().addClass('strength strength4');
                    strength_result.html('@lang('Translate::form.strong')');
                    break;
                case 5:
                    strength_class.removeClass().addClass('strength strength5');
                    strength_result.html('@lang('Translate::form.too_strong')');
                    break;
                default:
                    strength_class.removeClass().addClass('strength');
                    strength_result.html('@lang('Translate::form.strength')');
            }
        });
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
        {{-- click submit check lại độ mạnh mật khẩu --}}
        $('body').on('click', 'button[type=submit]', function(e) {
            password = $('#{{ $name??'' }}').val();
            strength = passwordStrength(password);
            if (strength < 3 && password.length > 0) {
                e.preventDefault();
                $('#{{ $name??'' }}').parent().find('.error').remove();
                $('#{{ $name??'' }}').parent().append(formHelper('@lang('Translate::form.password_strength')'));
                openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.password_strength')');
                $('#{{ $name??'' }}').css('border', '1px solid #ff0000');
            }
        });
    });
</script>