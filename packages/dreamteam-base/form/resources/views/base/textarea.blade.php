{{-- 
	@include('Form::base.textarea', [
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
    <textarea class="form-control" x-on:input="handleInput($event)" autocomplete="off" name="{{ $name ?? '' }}"
        id="{{ $name ?? '' }}" placeholder="@lang($placeholder ?? ($label ?? ($name ?? '')))" rows="{{ $row ?? '5' }}"
        @if ($disable == true) disabled @endif>{{ old($name) ?? ($value ?? '') }}</textarea>
    @if ($limit ?? 0)
        <span class="count" x-text="`${data.length}/${limit}`"
            style="position: absolute;
        right: 0;
        bottom: 0;
        background: #ccc;
        padding: 0 2px;
        font-size: 11px;"></span>
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
            validateInput('#{{ $name }}', '@lang($label ?? ($placeholder ?? ($name ?? ''))) @lang('Translate::form.valid.no_empty')');
        @endif
    });
</script>
