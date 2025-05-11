{{--
    @include('Form::base.input_color', [
        'name'              => $item['name'],
        'value'             => $item['value'],
        'required'          => $item['required'],
        'label'             => $item['label'],
        'placeholder'       => $item['placeholder'],
        'has_row'           => $item['has_row'],
        'class_col'         => $item['class_col'],
    ])
--}}

@if($class_col != '')
    <div class="{{ $class_col }}">
@endif
    <div class="mb-3 @if($has_row == true) row @endif" style="position: relative;">
        <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
        <div class="col-md-10" style="display: flex; justify-content: flex-start;">
            <input style="max-width: 120px;margin-right: 10px;" type="text" class="form-control color_code color-field" autocomplete="off" name="{{ $name??'' }}" id="{{ $name??'' }}" placeholder="@lang($placeholder??$label??$name??'')" value="{{ old($name)??$value??'' }}" @if($disable == true) disabled @endif>
            <input style="max-width: 120px;height: 38px;" type="color" class="form-control input_color color-field" value="{{ old($name)??$value??'' }}">
        </div>
    </div>

@if($class_col != '')
    </div>
@endif
<script>
    $(document).ready(function() {
        @if ($required==1)
            validateInput('#{{ $name??'' }}', '@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
        @endif

        $('body').on('change', '.input_color', function(){
            let val = $(this).val();
            $(this).parent().find('.color_code').val(val).change();
        })
        $('body').on('blur', '.color_code', function(){
            let val = $(this).val();
            if (val === '') {
                return;
            }
            $(this).parent().find('.input_color').val(val).change();
        })
    });
</script>
