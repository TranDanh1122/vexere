<td style="width: 150px;">
	@if(date('Y-m-d H:i:s') > $value->created_at)
		<span>{{ trans('Core::admin.general.publish') }}</span><br/>
	@else
		<span>{{ trans('Core::admin.general.publish_time') }}</span><br/>
	@endif
	{{ date('d/m/Y H:i', strtotime($value->created_at)) }}
</td>