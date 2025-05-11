{{-- 
	@include('Form::base.mutiCheckbox', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'options' 			=> $item['options'],
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
    <div class="form-check-info" style="max-height: 200px;overflow-y: scroll;">
        <input type="text" class="form-control" id="{{ $name??'' }}_search" placeholder="@lang('Translate::form.search') @lang($label??'')">
        <button type="button" class="btn btn-info" id="{{ $name??'' }}_checkall" title="@lang('Translate::form.check_all')"><i class="fa fa-check-double"></i></button>
        <div class="form-group-checkbox" id="{{ $name??'' }}" style="background: #f2f2f5;">
            @foreach ($options as $key => $option)
                <div class="form-checkbox" style="clear: both;">
                    <input type="checkbox" class="form-check-input" name="{{ $name??'' }}[]" id="{{ $name??'' }}_{{ $key??'' }}" value="{{ $key??'' }}"
                        @if (isset($value) && !empty($value))
                            @foreach ($value as $v)
                                @if ($v == $key) checked @endif
                            @endforeach
                        @endif
                    style="margin-left: 0;margin-right: 10px;font-size: 20px;">
                    <label for="{{ $name??'' }}_{{ $key??'' }}" style="padding-top: 5px;">@lang($option??'')</label>  
                </div>
            @endforeach
        </div>
    </div> 
    @if($has_row == true) 
        </div> 
    @endif
</div>
@if($class_col != '')
    </div>
@endif
<style>
    #{{ $name??'' }}_search {
        width: calc(100% - 40px);
        float: left;
    }
    #{{ $name??'' }}_checkall {
        width: 40px;
        float: left;
    }
    </style>
<script>
    $(document).ready(function() {
        // Input tìm kiếm
        $('body').on('change keyup', '#{{ $name??'' }}_search', function(e) {
            e.preventDefault();
            search_value = $(this).val().toLowerCase();
            $('#{{ $name??'' }}').find('.form-checkbox').each(function() {
                text = $(this).find('label').text().toLowerCase();
                if(text.indexOf(search_value) != -1){
                    $(this).css('display', 'block');
                } else {
                    $(this).css('display', 'none');
                }
            });
        })
        // Chọn tất cả
        check_all = 0;
        $.each( $('#{{ $name??'' }} input[name^={{ $name??'' }}]'), function() {
            if ($(this).prop('checked') == true) {
                check_all = 1;
            }
        });
        $('body').on('click', '#{{ $name??'' }}_checkall', function(e) {
            e.preventDefault();
            if (check_all == 0) {
                $('#{{ $name??'' }} input[name^={{ $name??'' }}]').prop('checked', true);
                check_all = 1;
                $('#{{ $name??'' }}').parent().find('.error').remove();
                $('#{{ $name??'' }}').css('border', '1px solid #f2f2f5');
            } else {
                $('#{{ $name??'' }} input[name^={{ $name??'' }}]').prop('checked', false);
                check_all = 0;
            }
        });
        // Nếu bắt buộc
        @if ($required==1)
            $('body').on('change', 'input[name^={{ $name??'' }}]', function() {
                $('#{{ $name??'' }}').parent().find('.error').remove();
                $('#{{ $name??'' }}').css('border', '1px solid #f2f2f5');
            });
            $('body').on('click','.form-actions button[type=submit]', function(e) {
                {{ $name??'' }}_array = [];
                $('input[name^={{ $name??'' }}]:checked').each(function(index, item) {
                    value = $(this).val();
                    {{ $name??'' }}_array.push(value);
                });
                $('#{{ $name??'' }}').parent().find('.error').remove();
                $('#{{ $name??'' }}').css('border', '1px solid #f2f2f5');
                if (checkEmpty({{ $name??'' }}_array)) {
                    e.preventDefault();
                    $('#{{ $name??'' }}').parent().append(formHelper('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.is_select')'));
                    openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.is_select')');
                    $('#{{ $name??'' }}').css('border', '1px solid #ff0000');
                }
            });
        @endif
    });
</script>