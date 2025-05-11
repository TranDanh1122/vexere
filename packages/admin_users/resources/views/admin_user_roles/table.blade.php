<td style="width: 300px;">
	<a href="{{ route('admin.admin_user_roles.edit', $value->id) }}">
		{{ $value->name }}
		@if($value->status == \DreamTeam\Base\Enums\BaseStatusEnum::DRAFT)
			<span class="badge badge-secondary status-label ms-2">{{ __('Core::admin.general.draf') }}</span>
		@endif
	</a>
</td>
<!-- <td style="width: 300px;">
	{{ $value->team }}
</td> -->
<td style="width: 440px;">
	{{-- Check có DreamTeamModule không --}}
	@if (!empty(config('DreamTeamModule.modules')))
		<div class="auth-box">
			{{-- Lặp Dreamteam Module để lấy từng module --}}
			@foreach (config('DreamTeamModule.modules') as $key => $modules)
				{{-- Check xem có phân quyền không => có thì hiển thị --}}
				@if (isset($modules['permision']) && !empty($modules['permision']))
					<div class="auth">
						<p class="auth-title">@lang($modules['name']??$module_name[$key]??'')</p>
						<div class="auth-list">
							{{-- Lấy từng quyền --}}
							@foreach ($modules['permision'] as $permision)
								<div class="item
									@if (in_array(str_replace($key.'_settings','settings', $key.'_'.$permision['type']), $value->getRole())) active @endif
								">@lang($permision['name']??$module_name[$permision['type']]??'')</div>
							@endforeach
						</div>
					</div>
				@endif
			@endforeach
		</div>
	@endif
</td>