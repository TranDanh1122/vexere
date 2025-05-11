@extends('Core::layouts.app')

@section('head')
<link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/logs-content.css') }}">
@endsection

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body logs">
				<h4 class="card-title">@lang('Core::admin.logs.title')</h4>
				<div class="col-lg-12 logs-info">
					<table class="table table-bordered logs-info__table">
						<thead>
							<tr>
								<th colspan="2" class="text-center">@lang('Core::admin.logs.info_title')</th>
							</tr>
						</thead>
						<tbody>
							@if (isset($admin_users) && !empty($admin_users))
								<tr>
									<td>@lang('Core::admin.logs.name')</td>
									<td>{!! $admin_users->getName() !!}</td>
								</tr>
							@endif
							@if (isset($systemLog->ip) && !empty($systemLog->ip))
								<tr>
									<td>@lang('Core::admin.logs.ip')</td>
									<td>{!! $systemLog->ip !!}</td>
								</tr>
							@endif
							@if (isset($systemLog->action) && !empty($systemLog->action))
								<tr>
									<td>@lang('Core::admin.logs.action')</td>
									<td>{!! $systemLog->getActionName() !!}</td>
								</tr>
							@endif
							@if (isset($systemLog->type) && !empty($systemLog->type))
								<tr>
									<td>@lang('Core::admin.logs.type')</td>
									<td>{!! $systemLog->getModuleName() !!}</td>
								</tr>
							@endif
							@if (isset($systemLog->type_id) && !empty($systemLog->type_id))
								<tr>
									<td>@lang('Core::admin.logs.type_id')</td>
									<td>{!! $systemLog->type_id !!}</td>
								</tr>
							@endif
							@if (isset($systemLog->time) && !empty($systemLog->time))
								<tr>
									<td>@lang('Core::admin.logs.time')</td>
									<td>{!! $systemLog->getTime('time') !!}</td>
								</tr>
							@endif
						</tbody>
					</table>
				</div>

				@switch($systemLog->action)
				    @case('create') 
						@php
				    		$detail = $systemLog->getDetail();
				    	@endphp
				    	<table class="table table-bordered logs-table">
							<thead>
								<tr>
									<th colspan="3" class="text-center">@lang('Core::admin.logs.detail')</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>@lang('Core::admin.logs.field')</th>
									<th>@lang('Core::admin.logs.data')</th>
								</tr>
								@if (isset($detail['fields']) && !empty($detail['fields']))
									@foreach ($detail['fields'] as $field)
										@if (!in_array($field, ['password']))
											@php
												$data_create = $detail['data'][$field] ?? '';
											@endphp
											<tr>
												<td>{!! __( config('DreamTeamModule.logs')[$systemLog->type][$field] ?? config('DreamTeamModule.logs_name')[$field] ?? $field ?? '' )  !!}</td>
												@if (in_array($field, config('DreamTeamModule.logs_content_field')))
													<td class="logs-content logs-content__limit">
														<div class="limit">{!! $old ??'' !!}</div>
													</td>
												@else
													<td>{!! $data_create ??'' !!}</td>
												@endif
											</tr>
										@endif
									@endforeach
								@endif
							</tbody>
						</table>
				    @break
				    @case('login') @break
				    @case('delete_forever')
						@php
				    		$detail = $systemLog->getDetail();
				    	@endphp
				    	<table class="table table-bordered logs-table">
							<thead>
								<tr>
									<th colspan="2" class="text-center">@lang('Core::admin.logs.detail')</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>@lang('Core::admin.logs.field')</th>
									<th>@lang('Core::admin.logs.old')</th>
								</tr>
								@if (isset($detail['fields']) && !empty($detail['fields']))
									@foreach ($detail['fields'] as $field)
										@if (!in_array($field, ['password']))
											@php
												$old = $detail['old'][$field] ?? '';
												$new = $detail['new'][$field] ?? '';
												if(is_array($old) || is_array($new)) continue;
											@endphp
											<tr>
												<td>{!! __( config('DreamTeamModule.logs')[$systemLog->type][$field] ?? config('DreamTeamModule.logs_name')[$field] ?? $field ?? '' )  !!}</td>
												@if (in_array($field, config('DreamTeamModule.logs_content_field')))
													<td class="logs-content logs-content__limit">
														<div class="limit">{!! $old ??'' !!}</div>
													</td>
												@else
													<td>{!! $old ??'' !!}</td>
												@endif
											</tr>
										@endif
									@endforeach
								@endif
							</tbody>
						</table>
						<form action="{{ route('admin.system_logs.rollback', ['id' => $systemLog->id ]) }}" class="confirmRollback" method="POST">
							@csrf
							<button type="submit" class="btn btn-success">{{ __('Core::admin.rollback_data') }}</button>
						</form>
						<script type="text/javascript">
							$(document).ready(function(){
								$('body').on('submit', '.confirmRollback', function(e){
									if(!confirm('{{ __('Core::admin.confirm_rollback') }}')) {
										e.preventDefault()
									}
								})
							})
						</script>
				    @break
				    @default
						@php
				    		$detail = $systemLog->getDetail();
				    	@endphp
				    	<table class="table table-bordered logs-table">
							<thead>
								<tr>
									<th colspan="3" class="text-center">@lang('Core::admin.logs.detail')</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>@lang('Core::admin.logs.field')</th>
									<th>@lang('Core::admin.logs.old')</th>
									<th>@lang('Core::admin.logs.new')</th>
								</tr>
								@if (isset($detail['fields']) && !empty($detail['fields']))
									@foreach ($detail['fields'] as $field)
										@if (!in_array($field, ['password']))
											@php
												$old = $detail['old'][$field] ?? '';
												$new = $detail['new'][$field] ?? '';
											@endphp
											<tr>
												<td>{!! __( config('DreamTeamModule.logs')[$systemLog->type][$field] ?? config('DreamTeamModule.logs_name')[$field] ?? $field ?? '' )  !!}</td>
												@if (in_array($field, config('DreamTeamModule.logs_content_field')))
													<td class="logs-content logs-content__limit">
														<div class="limit">{!! $old ??'' !!}</div>
													</td>
													<td class="logs-content logs-content__limit" @if($old != $new) style="background: #e6e6e6;" @endif>
														<div class="limit">{!! $new ??'' !!}</div>
													</td>
												@else
													<td>{!! $old ??'' !!}</td>
													<td class="" @if($old != $new) style="background: #e6e6e6;" @endif>{!! $new ??'' !!}</td>
												@endif
											</tr>
										@endif
									@endforeach
								@endif
							</tbody>
						</table>
				    @break
				@endswitch
			</div>
		</div>
	</div>
</div>
@endsection