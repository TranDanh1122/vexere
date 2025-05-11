<td style="min-width: 130px; width: 130px;" class="center"><a href="{{ route('admin.orders.show', $value->id) }}">{{$value->code ?? ''}}</a></td>
<td style="width: 120px">{{ date('d/m/Y H:i', strtotime($value->created_at)) }}</td>
<td style="min-width: 200px;">
	<p class="mb-1">{{ $value->customer->name ?? '' }}</p>
	<p class="mb-1"><strong><a href="tel:{{ $value->customer->phone ?? '' }}">{{ $value->customer->phone ?? '' }}</a></strong></p>
</td>
<td style="width: 140px;">
	{{-- {!! $value->status->toHtml() !!} --}}
	@if (isset($options) && count($options) > 0)
	    <select class="form-control input-sm" name="status" data-quick_edit>
	    	@foreach ($options as $key => $option)
	        	<option value="{{$key}}" @if($value->status->getValue() == $key){!! 'selected' !!} @endif()>@lang($option)</option>
	    	@endforeach
	    </select>
	@endif
</td>
<td style="width: 110px;">{{$value->getTotalPrice()}}</td>
