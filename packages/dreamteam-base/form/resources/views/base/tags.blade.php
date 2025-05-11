{{-- 
	@include('Form::base.tags', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'placeholder' 		=> $item['placeholder'],
    ])
--}}

@if($class_col != '')
    <div class="{{ $class_col }}">
@endif
<div class="form-group-tags mb-3 @if($has_row == true) row @endif" style="position: relative;">
    <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
    @if($has_row == true) 
        <div class="col-md-10">
    @endif
    <input type="text" class="form-control" autocomplete="off" id="{{ $name??'' }}_input" placeholder="@lang($placeholder??$label??$name??'')">
    @if($has_row == true) 
        </div> 
    @endif
    <div class="col-lg-3 col-md-2" style="position: absolute;padding-left: 0;right: 0;@if($has_row == true) top: 0; @else top: 27px; @endif">
        <button type="button" class="btn btn-primary" data-tags_{{ $name??'' }} style="width: 100%;">@lang('Translate::form.add_tags')</button>
    </div>
    <div class="row">
        @if($has_row == true)
        <label class="col-md-2"></label>
        @endif
        <div class="@if($has_row == true) col-md-10 @else col-md-12 @endif">
            <div class="tags">
                @if (isset($value) && !empty($value))
                    @foreach ($value as $tag)
                        <div class="tags-item">
                            <input type="hidden" name="{{ $name??'' }}[]" value="{{ $tag??'' }}">
                            <div class="tags-item__text">{{ $tag??'' }}</div>
                            <div class="tags-item__remove" data-delete_tags><i class="fa fa-trash"></i></div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@if($class_col != '')
    </div>
@endif
<script>
    $(document).ready(function() {
        {{-- Xóa tags item --}}
        $('body').on('click', '*[data-delete_tags]', function() {
            $(this).closest('.tags-item').remove();
        });
        {{-- tự động nhập khi input thay đổi --}}
        @if (isset($auto_click) && $auto_click == 1)
            auto_click = null;
            $('body').on('change keyup', '#{{ $name??'' }}_input', function() {
                e = $(this);
                value = $(this).val();
                clearTimeout(auto_click);
                if (!checkEmpty(value)) {
                    auto_click = setTimeout(function() {
                        e.closest('.form-group-tags').find('*[data-tags_{{ $name??'' }}]').click();
                    }, 2000);
                }
            });
        @endif
        {{-- Click vào nút thêm --}}
        $('body').on('click', '*[data-tags_{{ $name??'' }}]', function() {
            tag_value = $('#{{ $name??'' }}_input').val();
            if (!checkEmpty(tag_value)) {
                $(this).closest('.form-group-tags').find('.tags').append(`
                    <div class="tags-item">
                        <input type="hidden" name="{{ $name??'' }}[]" value="${tag_value}">
                        <div class="tags-item__text">${tag_value}</div>
                        <div class="tags-item__remove" data-delete_tags><i class="fa fa-trash"></i></div>
                    </div>
                `);
                $('#{{ $name??'' }}_input').css('border', '1px solid #ced4da').val('');
                $('#{{ $name??'' }}_input').parent().find('.error').remove();
                $('#{{ $name??'' }}_input').focus();
            }
        });
        {{-- Nếu bắt buộc --}}
        @if ($required==1)
            $('body').on('click','button[type=submit]', function(e) {
                {{ $name??'' }}_array = [];
                $('input[name^={{ $name??'' }}]').each(function(index, item) {
                    value = $(this).val();
                    {{ $name??'' }}_array.push(value);
                });
                $('#{{ $name??'' }}_input').css('border', '1px solid #ced4da');
                $('#{{ $name??'' }}_input').parent().find('.error').remove();
                if (checkEmpty({{ $name??'' }}_array)) {
                    e.preventDefault();
                    $('#{{ $name??'' }}_input').parent().append(formHelper('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.is_tags')'));
                    openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.is_tags')');
                    $('#{{ $name??'' }}_input').css('border', '1px solid #ff0000');
                }
            });
        @endif
    });
</script>