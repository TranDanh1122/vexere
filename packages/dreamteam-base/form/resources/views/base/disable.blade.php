{{-- 
	@include('Form::base.disable', [
        'value'             => $item['value'],
        'label'             => $item['label'],
    ])
--}}

@if($class_col != '')
    <div class="{{ $class_col }}">
@endif
    <div class="mb-3 @if($has_row == true) row @endif" style="position: relative;">
        <label for="{{ $name??'' }}" @if($has_row == true) class="col-md-2 col-form-label" @endif>@lang($label??'')</label>
        @if($has_row == true)
            <div class="col-md-10">
        @endif
        <input type="text" class="form-control" placeholder="@lang($label??'')" value="{{ $value??'' }}" disabled="disabled">
        @if($has_row == true) 
            </div> 
        @endif
    </div>

@if($class_col != '')
    </div>
@endif