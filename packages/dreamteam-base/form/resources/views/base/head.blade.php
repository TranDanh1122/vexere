{{-- 
	@include('Form::base.head', [
    	'label'				=> $item['label'],
        'has_row'             => $item['has_row'],
    ])
--}}
@if (isset($label) && !empty($label))
    @if(isset($has_row) && $has_row == true)
        <div class="mb-3 row " style="position: relative;">
            <p class="col-md-2"></p>
            <div class="col-md-10"><p style="font-size: 15px"><b>@lang($label??'')</b></p></div>
        </div>
    @else
        <p style="font-size: 15px"><b>@lang($label??'')</b></p>
    @endif
@endif
