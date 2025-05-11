{{-- 
	@include('Form::base.select', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'options' 			=> $item['options'],
		'select2' 			=> $item['select2'],
		'disabled' 			=> $item['disabled'],
    ])
--}}
<style>.select2-container {width: 100% !important;}</style>
@if(isset($cascader) && $cascader == 1)
    @if($class_col != '')
        <div class="{{ $class_col }}">
    @endif
        <div class="mb-3 @if($has_row == true) row @endif">
            <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
            @if($has_row == true)
                <div class="col-md-10">
            @endif
                <input type="text" placeholder="Chọn {{ $label??'' }}" class="form-control" id="{{ $name??'' }}"/>
                <input type="hidden" name="{{ $name??'' }}" id="value_{{ $name??'' }}" value="{{ $value ?? '' }}">
            @if($has_row == true)
                </div>
            @endif
        </div>

    @if($class_col != '')
        </div>
    @endif
    <script>
        function findParentValues(array, value, parentValues = []) {
            for (let i = 0; i < array.length; i++) {
                const item = array[i];
                if (item.value === value) {
                    parentValues.push(value)
                    return parentValues;
                }
                if (item.children) {
                    const foundParentValues = findParentValues(item.children, value, parentValues.concat(item.value));
                    if (foundParentValues.length > 0) {
                        return foundParentValues;
                    }
                }
            }
            return [];
        }
        $(document).ready(function() {
            {{-- Nếu bắt buộc --}}
            @if ($required==1)
                validateInput('#{{ $name??'' }}', '@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.is_select')')
            @endif
            @if(isset($cascader) && $cascader == 1)
                let jsonString = '{!! str_replace("'", "", json_encode($options??[])) !!}'
                var escapedJsonString = jsonString.replace(/[\u0000-\u001F\u007F-\u009F]/g, function (match) {
                    return '\\u' + ('0000' + match.charCodeAt(0).toString(16)).slice(-4);
                });
                let dataCascader = JSON.parse(escapedJsonString)
                $('#{{ $name??'' }}').zdCascader({
                    data: dataCascader,
                    selectedItemId: findParentValues(dataCascader, parseInt('{{ $value ?? 0 }}')),
                    inputValueName: '{{ $name??'' }}',
                    search: true,
                    container: '#{{ $name??'' }}',
                    onChange: function(value, label, datas){
                        let val = label.value;
                        if(Array.isArray(val)) {
                            val = val.pop()
                        }
                        $(`#value_{{ $name??'' }}`).val(val).change()
                        $(`#{{ $name??'' }}`).parent().find('.error.helper').remove()
                    }
                  });
            @endif
        });
    </script>
@else
    @if($class_col != '')
        <div class="{{ $class_col }}">
    @endif
        <div class="mb-3 @if($has_row == true) row @endif">
            <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@if($required==1) * @endif @lang($label??'')</label>
            @if($has_row == true)
                <div class="col-md-10">
            @endif
            <select class="form-control form-select" name="{{ $name??'' }}" id="{{ $name??'' }}">
                @foreach ($options as $key => $option)
                    <option value="{{ $key??'' }}"
                        {{-- chọn hay không --}}
                        @if (isset($value) && !empty($value))
                            @if ($key == $value) selected @endif
                        @endif
                        {{-- Có ẩn chọn hay không --}}
                        @if (isset($disabled) && !empty($disabled))
                            @foreach ($disabled as $dis)
                                @if ($dis == $key) disabled="disabled"  @endif
                            @endforeach
                        @endif
                    >@lang($option??'')</option>
                @endforeach
            </select>
            @if($has_row == true)
                </div>
            @endif
        </div>

    @if($class_col != '')
        </div>
    @endif
    <script>
        $(document).ready(function() {
            {{-- Nếu có giá trị select2 --}}
            @if (isset($select2) && $select2 == 1)
                $('#{{ $name??'' }}').select2();
            @endif
            {{-- Nếu bắt buộc --}}
            @if ($required==1)
                validateSelect('#{{ $name??'' }}', '@lang($label??$placeholder??$name??'') @lang('Translate::form.valid.is_select')')
            @endif
        });
    </script>
@endif
