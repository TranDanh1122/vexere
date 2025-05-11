{{-- 
	@include('Form::base.editor', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
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
        <textarea class="form-control" name="{{ $name??'' }}" id="{{ $name??'' }}">{!! removeScript(old($name) ?? $value ?? '') !!}</textarea>
        @if($has_row == true) 
            </div> 
        @endif
    </div>

@if($class_col != '')
    </div>
@endif
<script>
    $(document).ready(function() {
        addTinyMCE('#{{ $name??'' }}');
        @if ($required==1)
            $('body').on('click', 'button[type=submit]', function(e) {
                content = tinyMCE.get('{{ $name??'' }}').getContent();
                if (checkEmpty(content)) {
                    $('#{{$name??''}}_box').find('.error').remove();
                    $('#{{$name??''}}_box').append(formHelper('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')'));
                    openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
                }
            });
        @endif
    });
</script>