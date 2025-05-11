{{-- 
	@include('Form::base.text', [
    	'name'				=> $item['name'],
		'value' 			=> $item['value'],
		'required' 			=> $item['required'],
		'label' 			=> $item['label'],
		'placeholder' 		=> $item['placeholder'],
        'has_row'           => $item['has_row'],
        'class_col'         => $item['class_col'],
    ])
--}}

@if ($class_col != '')
    <div class="{{ $class_col }}">
@endif
<div class="mb-3 @if ($has_row == true) row @endif" x-data="{
    data: '{{ old($name) ?? ($value ?? '') }}',
    limit: parseInt('{{ $limit ?? 0 }}'),
    handleInput(event) {
        if (this.limit == 0 || event.target.value.length <= this.limit) {
            this.data = event.target.value;
        } else {
            event.target.value = this.data;
        }
    }
}" style="position: relative">
    <label for="{{ $name ?? '' }}" @if ($has_row == true) class="col-md-2 col-form-label" @endif>
        @if ($required == 1)
            *
        @endif @lang($label ?? '')
    </label>
    @if ($has_row == true)
        <div class="col-md-10">
    @endif
    <input type="text" class="form-control" x-on:input="handleInput($event)" autocomplete="off"
        name="{{ $name ?? '' }}" id="{{ $name ?? '' }}" placeholder="@lang($placeholder ?? ($label ?? ($name ?? '')))"
        value="{{ old($name) ?? ($value ?? '') }}" @if ($disable == true) disabled @endif @if ($limit ?? 0) style="padding-right: 50px; " @endif>
    @if ($limit ?? 0)
        <span class="count" x-text="`${data.length}/${limit}`"
            style="position: absolute;
            right: 0;
            bottom: 0;
            background: #556ee6;
            width: 48px;
            padding: 0 2px;
            font-size: 11px;
            height: 36px;
            text-align: center;
            border-radius: 0 5px 5px 0;
            line-height: 36px;
            color: #fff;"></span>
    @endif
    {{-- Nếu là module link đồng bộ thì thêm domain trước liên kết nguồn --}}
    @if ($name == 'old')
        <span class="sync-links-old">{!! env('APP_URL') !!}</span>
    @endif

    @if ($has_row == true)
</div>
@endif
</div>

@if ($class_col != '')
    </div>
@endif
<script>
    $(document).ready(function() {
        @if ($required == 1)
            validateInput('#{{ $name ?? '' }}', '@lang($label ?? ($placeholder ?? ($name ?? ''))) @lang('Translate::form.valid.no_empty')');
        @endif

        // Nếu là module link đồng bộ thì thêm domain trước liên kết nguồn
        @if ($name == 'old')
            var width_domain = $('.sync-links-old').width() + 30 + 10;
            $('.sync-links-old').parents('.mb-3').find('input').css('padding-left', width_domain + 'px');
        @endif
    });
</script>
