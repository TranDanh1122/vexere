{{-- 
	@include('Form::base.multiSuggest', [
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
    <div class="mb-3 @if($has_row == true) row @else suggest @endif">
        <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
        @if($has_row == true) 
            <div class="suggest col-md-10">
        @endif
        <input type="text" class="form-control" autocomplete="off" id="{{ $name??'' }}_input" placeholder="@lang($placeholder??$label??$name??'')" value="{{ $data->$suggest_name??'' }}">
        <div class="suggest-result" @if($has_row == true) style="top: 37px;padding: 0 11px;" @endif><ul></ul></div>
        <div class="col-lg-12 tags" id="{{ $name??'' }}_box">
            @php
                $value = old($name)??$value;
                if (is_array($value)) {
                    $id_array = $value;
                } else {
                    $id_array = explode(',', $value);
                }
                $ids_ordered = implode(',', $id_array);
                $data = [];
                if(!empty($ids_ordered)) {
                    $data = \DB::table($suggest_table)->whereIn('id', $id_array)
                    ->orderByRaw("FIELD(id, $ids_ordered)")->where('status', 1)->get();
                }
            @endphp
            @foreach ($data as $item)
                <div class="tags-item">
                    <input type="hidden" name="{{ $name??'' }}[]" value="{{ $item->$suggest_id??'' }}">
                    <div class="tags-item__text">{{ $item->$suggest_name??'' }}</div>
                    <div class="tags-item__remove" data-delete_tags><i class="fa fa-trash"></i></div>
                </div>
            @endforeach
        </div>
        @if($has_row == true) 
            </div> 
        @endif
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
        {{-- Nếu tìm kiếm tại form --}}
        suggest = null;
        $('body').on('keyup', '#{{ $name??'' }}_input', function(event) {
            const keyword = $(this).val();
            clearTimeout(suggest);
            const e = $(this);
            if (keyword.length > 0) {
                suggest = setTimeout(function() {
                    {{-- Chuẩn hóa data --}}
                    data = {
                        table: '{{$suggest_table??''}}', 
                        id: '{{$suggest_id??''}}', 
                        name: '{{$suggest_name??''}}', 
                        suggest_locale: '{{$suggest_locale??'false'}}',
                        keyword: keyword
                    };
                    {{-- Không tìm theo sản phẩm đã lấy --}}
                    id_not_where = [];
                    $.each($('input[name="{{ $name??'' }}[]"]') ,function() {
                        id_not_where.push($(this).val());
                    });
                    if(!checkEmpty(id_not_where)) {
                        data.id_not_where = id_not_where;
                    }
                    loadAjaxPost('{{ route('admin.ajax.suggest_search', ['lang_locale' => $lang_locale ?? Request()->lang_locale ?? \App::getLocale()]) }}', data, {
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
            $(this).css('display', 'none');
            const id = $(this).data('id');
            const text = $(this).text();
            $('#{{ $name??'' }}_box').append(`
                <div class="tags-item">
                    <input type="hidden" name="{{ $name??'' }}[]" value="${id}">
                    <div class="tags-item__text">${text}</div>
                    <div class="tags-item__remove" data-delete_tags><i class="fa fa-trash"></i></div>
                </div>
            `);
            $('#{{$name??''}}_box').find('.error').remove();
            $('#{{ $name??'' }}_input').css('border', '1px solid #ced4da');
        });
        {{-- Nếu click ra vùng bất kỳ thì ẩn box suggest --}}
        $(document).bind('click', function(e) {
            const clicked = $(e.target);
            if (!clicked.parent('ul').parent().hasClass('suggest-result'))
                $('.suggest-result').css('display','none').find('ul').empty();
        });
        {{-- Nếu bắt buộc --}}
        @if ($required==1)
            $('body').on('click','button[type=submit]', function(e) {
                $('#{{ $name??'' }}_input').css('border', '1px solid #ced4da');
                $('#{{$name??''}}_box').find('.error').remove();
                if($('input[name="{{$name??''}}[]"]').length == 0) {
                    e.preventDefault();
                    $('#{{$name??''}}_box').append(formHelper('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')'));
                    openPopup('@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.no_empty')');
                    $('#{{ $name??'' }}_input').css('border', '1px solid #ff0000');
                }
            });
        @endif
        // sort
        $('#{{ $name??'' }}_box').sortable();
    });
</script>