<td style="width: {!! $width ?? 'auto' !!};white-space: inherit; {{ !empty($width) ? ('min-width: '.$width) : '' }}" class="action-hover">
	<a href="{{ $route }}">
		{!! $name !!}
		@if($value->status == \DreamTeam\Base\Enums\BaseStatusEnum::DRAFT)
			<span class="badge badge-secondary status-label ms-2">{{ __('Core::admin.general.draf') }}</span>
		@endif
		@if(isset($hasAdditionName) && $hasAdditionName)
			{!! do_action(ADMIN_SHOW_ADDITION_PAGE_NAME, PAGE_MODULE_SCREEN_NAME, $value) !!}
		@endif
	</a>
	<div class="action-hover__list">
		@if(isset($data['userRoleActions'][$table_name . '_edit']) && $data['userRoleActions'][$table_name . '_edit'])
			<a href="{{ $route }}" style="color: #f1b44c">{{ __('Core::admin.general.edit_record') }}</a>
		@endif
		@if(isset($duplicate) && !\Request()->trash && isset($data['userRoleActions'][$table_name . '_create']) && $data['userRoleActions'][$table_name . '_create'])
			| <a href="{{ $duplicate }}" style="color: #2ca67a" target="_blank">{{ __('Core::admin.general.duplicate') }}</a>
		@endif
		@if(\Request()->trash && isset($data['userRoleActions'][$table_name . '_deleteForever']) && $data['userRoleActions'][$table_name . '_deleteForever'])
			| <a href="javascript:;" style="color: red;" data-delete_forever data-id="{{ $value->id }}" data-message="@lang('Translate::table.delete_forever_question')">{{ trans('Core::admin.general.delete_forever') }}</a>
		@elseif(isset($data['userRoleActions'][$table_name . '_delete']) && $data['userRoleActions'][$table_name . '_delete'])
			| <a href="javascript:;" style="color: red;" data-quick_delete data-message="@lang('Translate::table.delete_question')" style="cursor: pointer;">{{ trans('Translate::table.delete_temp') }}</a>
		@endif
		@if (! empty($view))
			| <a href="{{ $view }}" style="color: #333" target="_blank">{{ trans('Core::admin.general.show') }}</a>
		@endif
	</div>
</td>