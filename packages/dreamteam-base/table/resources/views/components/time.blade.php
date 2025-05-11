<td style="width: 230px;">
	@if (isset($value->created_at) && !empty($value->created_at))
    	<p class="m-0"><strong>@lang('Translate::table.created_at'):</strong> {!! date('H:i:s d/m/Y', strtotime($value->created_at)) !!}</p>
	@endif
	@if (isset($value->updated_at) && !empty($value->updated_at))
    	<p class="m-0"><strong>@lang('Translate::table.updated_at'):</strong> {!! date('H:i:s d/m/Y', strtotime($value->updated_at)) !!}</p>
    @endif
	@if (isset($value->time) && !empty($value->time))
    	<p class="m-0"><strong>@lang('Translate::table.time'):</strong> {!! date('H:i:s d/m/Y', strtotime($value->time)) !!}</p>
    @endif
</td>