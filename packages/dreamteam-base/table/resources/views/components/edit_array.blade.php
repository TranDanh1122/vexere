<td class="center table-array">
	@if (isset($options) && count($options) > 0)
	    <select class="form-control input-sm" name="{!! $name??'' !!}" data-quick_edit>
	    	@foreach ($options as $key => $option)
	        	<option value="{{$key}}" @if($value==$key){!! 'selected' !!} @endif()>@lang($option)</option>
	    	@endforeach
	    </select>
	@endif
</td>