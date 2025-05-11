{{-- 
	@include('Form::base.suggest', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'placeholder' 		=> $item['placeholder'],
    	'suggest_table' 	=> $item['suggest_table'], 
    	'suggest_id' 		=> $item['suggest_id'], 
    	'suggest_name' 		=> $item['suggest_name']
    ])
--}}
@if($class_col != '')
    <div class="{{ $class_col }}">
@endif
    <div class="suggest mb-3 @if($has_row == true) row @endif">
        <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
        @if($has_row == true) 
            <div class="col-md-10">
        @endif
        @php
            $data = \DB::table($suggest_table)->where($suggest_id, old($name)??$value)->where('status', 1)->first();
        @endphp
        <input type="text" class="form-control" autocomplete="off" id="{{ $name??'' }}_input" placeholder="@lang($placeholder??$label??$name??'')" value="{{ $data->$suggest_name??'' }}">
        <input type="hidden" name="{{ $name??'' }}" value="{{ $data->$suggest_id??'' }}">
        <div class="suggest-result"><ul></ul></div>

        @if($has_row == true) 
            </div> 
        @endif
    </div>

@if($class_col != '')
    </div>
@endif
<script>
    $(document).ready(function() {
        {{-- Nếu giá trị thay đổi --}}
        $('body').on('keyup change', 'input[name={{ $name??'' }}]', function() {
            $('#{{$name??''}}_input').parent().find('.error').remove();
            $('#{{$name??''}}_input').css('border', '1px solid #ced4da');
        });
        {{-- Nếu tìm kiếm tại form --}}
        suggest = null;
        $('body').on('keyup', '#{{ $name??'' }}_input', function(e) {
            keyword = $(this).val();
            clearTimeout(suggest);
            e = $(this);
            if (keyword.length > 0) {
                suggest = setTimeout(function() {
                    {{-- Chuẩn hóa data --}}
                    data = {
                        table: '{{$suggest_table??''}}', 
                        id: '{{$suggest_id??''}}', 
                        name: '{{$suggest_name??''}}', 
                        suggest_locale: '{{$suggest_locale??'0'}}', 
                        keyword: keyword
                    };
                    {{-- Không tìm theo sản phẩm đã lấy --}}
                    value = $('input[name={{ $name??'' }}]').val();
                    if(!checkEmpty(value)) {
                        data.id_not_where = [ value ];
                    }
                    loadAjaxPost('{{ route('admin.ajax.suggest_search') }}', data, {
                        beforeSend: function(){},
                        success:function(result){
                            if (result.status == 1) {
                                list = '';
                                $.each(result.data, function(index, item) {
                                    list += `<li class="suggest-item" data-suggest_{{$name??''}} data-id="${item.id}">${item.name}</li>`;
                                });
                                e.closest('.suggest').find('.suggest-result').css('display','block').find('ul').html(list);
                            } else {
                                alertText('@lang('Translate::form.no_data_finding')', 'error');
                            }
                        },
                        error: function (error) {}
                    }, 'progress');
                }, 1000);
            }
        });
        {{-- Nếu click vào item sẽ lấy giá trị tại item đó --}}
        $('body').on('click','*[data-suggest_{{$name??''}}]',function(e) {
            e.preventDefault();
            id = $(this).data('id');
            text = $(this).text();
            $(this).closest('.suggest').find('#{{ $name??'' }}_input').val(text);
            $(this).closest('.suggest').find('input[name={{ $name??'' }}]').val(id).change();
            $(this).closest('.suggest-result').css('display','none').find('ul').empty();
        });

        {{-- Nếu click ra vùng bất kỳ thì ẩn box suggest --}}
        $(document).bind('click', function(e) {
            var clicked = $(e.target);
            if (!clicked.parent('ul').parent().hasClass('suggest-result'))
                $('.suggest-result').css('display','none').find('ul').empty();
        });

        {{-- Nếu bắt buộc --}}
        @if ($required==1)
            $('body').on('click','button[type=submit]', function(e) {
                if(checkEmpty($('input[name={{$name??''}}]').val())) {
                    e.preventDefault();
                    $('#{{$name??''}}_input').css('border', '1px solid #ff0000');;
                    $('#{{$name??''}}_input').parent().find('.error').remove();
                    $('#{{$name??''}}_input').parent().append(formHelper('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')'));
                    openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
                }
            });
        @endif
    });
</script>