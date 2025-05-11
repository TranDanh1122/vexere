<td style="width: {!! $width ?? 'auto' !!};">
	<a href="{{$url??'javascript:;'}}">
		{{$text??''}}
		@if($value->status == \DreamTeam\Base\Enums\BaseStatusEnum::DRAFT)
			<span class="badge badge-secondary status-label ms-2">{{ __('Core::admin.general.draf') }}</span>
		@endif
	</a>
</td>