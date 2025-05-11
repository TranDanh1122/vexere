@extends('Core::layouts.app')
@section('head')
	<style>.action-hover__list {opacity: 0;visibility: hidden;padding-top: .4rem;}.action-hover:hover .action-hover__list {opacity: 1; visibility: visible;}.input-transparent{background:0 0;height:31px;border:none;outline:0}</style>
	<style>
		.listdata .header-response {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
		}

		.header-response > div {
			float: unset;
		}
		.header-response .float-left {
			flex: 1;
		}
		@media screen and (max-width: 576px) {
			.header-response .box-action {
				margin-bottom: 10px;
			}
			.header-response .float-left .form-group {
				width: 100%;
			}
			.header-response .float-left .form-group .form-control {
				width: 100%;
			}

			.header-response .float-right {
				flex-basis: 100%;
				max-width: unset !important;
			}
			.action-hover .action-hover__list {
				opacity: 1;
				visibility: visible;
			}
		}
		#status {
			position: relative;
			margin: inherit;
			width: auto;
			height: auto;
			top: unset;
			left: unset;
			display: block !important;
		}
	</style>
@endsection
@section('content')
<div class="top_html">
    @if (isset($include_view_top) && !empty($include_view_top))
    	@foreach ($include_view_top as $include_view => $include_data)
    		@include($include_view, $include_data)
    	@endforeach
    @endif
</div>
{!! apply_filters(FILTER_LIST_DATA_TABLE_TOP_VIEW, null, $table_name, \Request()) !!}
@php
	$languages = config('app.language');
	$defaultLang = $languages[config('app.fallback_locale')];
	unset($languages[config('app.fallback_locale')]);
	$languages = array_merge([config('app.fallback_locale') => $defaultLang], $languages);
@endphp
<div class="row">
	<div class="col-lg-12">
		<div class="card listdata" id="listdata">
			<div class="card-header header-response">
				@if (isset($data['search']) && !empty($data['search']))
					<div class="float-left search-container" style="width: calc(100% - 274px);">
						@if (Request()->trash == true)
							<div class="box-action">
								<select class="btn btn-default btn-sm" data-action data-field="{!! $data['action']['field_name'] !!}" style="float: left;height: 30px;margin-right: 5px;">
										<option value="-2">{!! __('Translate::table.batch_process') !!}</option>
										<option value="1">{!! __('Core::admin.general.restore') !!}</option>
										<option value="-3">{!! __('Core::admin.delete_forever') !!}</option>
								</select>
								<button style="float: left;padding: 5px 10px;margin-top: 1px;margin-right: 5px;"
									type="button"
									class="btn btn-sm btn-primary"
									data-table="{{$table_name??''}}"
									data-action_all
									data-warning="{{ __('Core::admin.please_select_action') }}"
									data-message="{{ __('Translate::table.delete_forever_question') }}"
									data-null="{{ __('Core::admin.no_data_delete') }}"
								> {!! __('Translate::table.apply') !!}</button>
							</div>
	                    @else
							@if(isset($data['action']) && is_array($data['action']))
							<div class="box-action">
								<select class="btn btn-default btn-sm" data-action data-field="{!! $data['action']['field_name'] !!}" style="float: left;height: 30px;margin-right: 5px;">
									@foreach($data['action']['value'] as $key => $value)
										<option value="{!! $value !!}">{!! __($data['action']['label'][$key]) !!}</option>
									@endforeach
								</select>
								<button style="float: left;padding: 5px 10px;margin-top: 1px;margin-right: 5px;"
									type="button"
									class="btn btn-sm btn-primary"
									data-table="{{$table_name??''}}"
									data-action_all
									data-warning="{{ __('Core::admin.please_select_action') }}"
								> {!! __('Translate::table.apply') !!}</button>
							</div>
							@endif
	                    @endif
						<form action="{{ route('admin.'.$table_name.'.index') }}" class="form-inline" method="GET" accept-charset="utf-8">
							<input type="hidden" name="search" value="1">
                            <input type="hidden" class="newUpdateUrl">
							@foreach ($data['search'] as $search)
								@switch($search['field_type']??'')
								    @case('string')
								    @case('custom_conditions')
								    	@php
								    		$fields = $search['fields']??'';
								    		$label = $search['label']??'';
								    	@endphp
								        <div class="form-group">
											<input type="text" class="form-control input-sm" name="{{ $fields }}" placeholder="@lang($label)" value="{{ Request()->$fields }}">
										</div>
							        @break

                                    @case('multipleColumns')
                                        @php
                                            $fields = $search['fields'] ?? [];
                                            $label = $search['label']??'';
                                            $isShow = true;
                                        @endphp
                                        <div class="form-group">
                                            <div class="d-flex align-items-center search-multiple-columns" style="background: #fff; border-radius: 3px;font-size:12px;border:1px solid #ced4da;height:31px">
                                                <div>
                                                    <select class="input-transparent search-column-selector" style="width:auto;padding:0 4px">
                                                        @foreach ($fields as $key => $value)
                                                            <option value="{{ $key }}">{{ is_array($value) ? $value['label'] : $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @foreach ($fields as $key => $value)
                                                    @if (is_string($value))
                                                        <input class="input-transparent search-value {{ $isShow ? '' : 'd-none' }}" name="{{ $key }}" placeholder="{{ $label }}" type="text" style="padding: 2px 6px;">
                                                    @elseif (is_array($value))
                                                        @switch($value['inputType'] ?? '')
                                                            @case('date')
                                                                <input class="input-transparent search-value {{ $isShow ? '' : 'd-none' }}" name="{{ $key }}" placeholder="{{ $label }}" type="date" style="padding: 2px 6px;">
                                                                @break
                                                            @default
                                                                <input class="input-transparent search-value {{ $isShow ? '' : 'd-none' }}" name="{{ $key }}" placeholder="{{ $label }}" type="text" style="padding: 2px 6px;">
                                                        @endswitch
                                                    @endif
                                                    @php
                                                        $isShow = false;
                                                    @endphp
                                                @endforeach
                                            </div>
                                        </div>
                                    @break

							        @case('custom_conditions_array')
							        @case('array')
										@php
								    		$fields = $search['fields']??'';
								    		$label = $search['label']??'';
								    		$options = $search['option']??'';
								    	@endphp
								    	<div class="form-group">
								    		<select id="{{ $fields }}" name="{{ $fields }}" class="form-control input-sm form-select">
								    			<option value="" @if(empty(Request()->$fields)) selected @endif >@lang('Translate::table.all') @lang($label)</option>
								    			@foreach ($options as $key => $option)
								    				<option value="{{ $key??'' }}"
								    					@if (Request()->$fields != null && Request()->$fields == $key)
									    					selected
									    				@endif
								    				>@lang($option??'')</option>
								    			@endforeach
								    		</select>
								    	</div>
							        @break

							        @case('hidden')
										@php
								    		$fields = $search['fields']??'';
								    		$label = $search['label']??'';
								    	@endphp
										<input type="hidden" class="form-control input-sm" name="{{ $fields }}" value="{{ Request()->$fields }}">
							        @break

							        @case('custom_conditions_range')
							        @case('range')
										@php
								    		$fields = $search['fields']??'';
								    		$field_start = $search['fields'].'_start'??'';
								    		$field_end = $search['fields'].'_end'??'';
								    		$label = $search['label']??'';
								    	@endphp
										<div class="form-group">
											<div id="{!! $fields !!}" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%" class="form-control input-sm">
		                                        <i class="fa fa-calendar mr-1"></i>
		                                        <span>{{ $label??'' }}</span>
		                                        <i class="fa fa-caret-down ml-1"></i>
		                                    </div>
	                                        <input id="{!! $fields !!}_start" type="hidden" name="{!! $fields !!}_start" value="">
	                                        <input id="{!! $fields !!}_end" type="hidden" name="{!! $fields !!}_end" value="">
										</div>
										<script>
											$(function() {
		                                        var start = moment('{{Request()->$field_start?? now()->format('d/m/Y')}}', 'YYYY-MM-DD HH:mm:ss');
		                                        var end = moment('{{Request()->$field_end??''}}', 'YYYY-MM-DD HH:mm:ss');
		                                        function cb(start, end) {
		                                            if (!end._isValid) {
		                                                $('#{!! $fields !!} span').html('@lang('Translate::table.all') @lang($label)');
		                                                $('#{!! $fields !!}_start').val('');
		                                                $('#{!! $fields !!}_end').val('');
		                                            }else {
		                                                $('#{!! $fields !!} span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
		                                                $('#{!! $fields !!}_start').val(start.format('YYYY-MM-DD HH:mm:ss'));
		                                                $('#{!! $fields !!}_end').val(end.format('YYYY-MM-DD HH:mm:ss'));
		                                            }
		                                        }
		                                        $('#{!! $fields !!}').daterangepicker({
		                                            startDate: start,
		                                            endDate: end,
		                                            timePicker: true,
		                                            timePicker24Hour: true,
		                                            timePickerSeconds: true,
		                                            ranges: {
		                                               '@lang('Core::admin.general.all')': [moment('1970-01-01'), moment().endOf('day')],
		                                               '@lang('Core::admin.general.today')': [moment().startOf('day'), moment().endOf('day')],
		                                               '@lang('Core::admin.general.yesterday')': [moment().startOf('day').subtract(1, 'days'), moment().endOf('day').subtract(1, 'days')],
		                                               '@lang('Core::admin.general._07_days')': [moment().startOf('day').subtract(6, 'days'), moment()],
		                                               '@lang('Core::admin.general._30_days')': [moment().startOf('day').subtract(29, 'days'), moment()],
		                                               '@lang('Core::admin.general.this_month')': [moment().startOf('month'), moment().endOf('month')],
		                                            },
		                                            locale: {
		                                                applyLabel: "@lang('Core::admin.general.select')",
		                                                cancelLabel: "@lang('Core::admin.general.delete')",
		                                                fromLabel: "@lang('Core::admin.general.from')",
		                                                toLabel: "@lang('Core::admin.general.to')",
		                                                customRangeLabel: "@lang('Core::admin.general.option')",
		                                                daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
		                                                monthNames: ["@lang('Core::admin.general.month') 1", "@lang('Core::admin.general.month') 2", "@lang('Core::admin.general.month') 3", "@lang('Core::admin.general.month') 4", "@lang('Core::admin.general.month') 5", "@lang('Core::admin.general.month') 6", "@lang('Core::admin.general.month') 7", "@lang('Core::admin.general.month') 8", "@lang('Core::admin.general.month') 9", "@lang('Core::admin.general.month') 10", "@lang('Core::admin.general.month') 11", "@lang('Core::admin.general.month') 12"],
		                                                firstDay: 1,
                                                        format: 'DD/MM/YYYY'
		                                            }
		                                        }, cb);
		                                        cb(start, end);
		                                    });
										</script>
							        @break
								@endswitch

							@endforeach
							{!! apply_filters(RENDER_FILTER_LIST_DATA_TABLE, null, $table_name, \Request()) !!}
							<div class="form-group">
	                        	<div class="btn-group">
	                        		@csrf
	                        		<button type="submit" class="btn btn-flat btn-success btn-sm search-btn"><i class="fas fa-search mr-1"></i>@lang('Translate::table.search')</button>
	                        		@if (isset($data['search_btn']) && !empty($data['search_btn']))
	                        			@foreach ($data['search_btn'] as $search_btn)
	                        				<button type="submit" formaction="{!! $search_btn['url']??'' !!}" formmethod="POST" class="btn btn-{!! $search_btn['btn_type']??'' !!} btn-flat btn-sm">{!! (isset($search_btn['btn_icon']) && !empty($search_btn['btn_icon']))?'<i class="'.$search_btn['btn_icon'].' mr-1"></i>' : ''!!}@lang($search_btn['label']??'')</button>
	                        			@endforeach
	                        		@endif
	                        	</div>
	                        </div>
						</form>
						{{-- Top Action --}}
                		@if (isset($data['top_action']) && !empty($data['top_action']))
                			@foreach ($data['top_action'] as $top_action)
                				{!! $top_action['action'] !!}
                			@endforeach
                		@endif
					</div>
				@endif
				<div class="float-right action-container" style="max-width: 274px;">
					@if (isset($data['no_add']) && $data['no_add'] == true)
						<a href="{{ route('admin.'.$table_name.'.create') }}" class="btn btn-sm btn-success"><i class="fa fa-plus mr-2"></i>@lang('Translate::table.add')</a>
					@endif
                    @if (Request()->trash == true)
						<a href="{{ route('admin.'.$table_name.'.index') }}" class="btn btn-sm btn-default"><i class="fas fa-bars mr-1"></i>@lang('Translate::table.list_url')</a>
                    @endif
				</div>
			</div>
			<!-- /.card-header -->

			<div class="table-rep-plugin">
				<div class="table-wrapper">
					<div class="table-responsive mb-0 fixed-solution" data-pattern="priority-columns">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="text-center pl-3" style="width: 50px;">{{__('Core::admin.numerical_order')}}</th>
									@if (isset($data['action']) && !empty($data['action']))
										<th class="table-checkbox center" style="width: 50px;">
											<input type="checkbox" class="form-check-input btn-checkbox checkall">
										</th>
									@endif
									@foreach ($data['table_generate'] as $generate)
										{{-- Nếu là route sửa và có đa ngôn ngữ thì hiển thị tự động cột đa ngôn ngữ --}}
										@if ($generate['type'] == 'lang')
											@if (isset($has_locale) && $has_locale == true && !empty($languages ?? []) && count($languages) > 1)
												<th class="lang center" style="width: calc(26px * {{count($languages ?? [])}} + 50px)">
													@foreach ($languages as $key => $lang)
														<span><img src="{{url($lang['flag'] ?? '')}}"></span>
													@endforeach
												</th>
											@endif
										@else
											<th
												class="text-center"
												@if (isset($generate['has_order']) && $generate['has_order'] == 1)
													style="cursor: pointer;"
													data-order_fields="{{$generate['type'] == 'pin' ? 'pin_' : ''}}{{$generate['field']??''}}"
													data-order_by="asc"
												@endif
											>
												@lang($generate['label']??'')
												@if (isset($generate['has_order']) && $generate['has_order'] == 1)
													<div class="float-right"><i class="fas fa-sort"></i></div>
												@endif
											</th>
										@endif
									@endforeach
								</tr>
							</thead>
							<tbody>
								@include('Table::item')
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- /.card-body -->

			<div class="card-footer clearfix">
				<div class="float-left footer-action">
					@if (!empty( config('app.page_size') ?? [] ))
						<select class="btn btn-default btn-sm" data-pagesize>
							@php
								$cookie_locale = (isset($_COOKIE['dreamteam_page_size']))? $_COOKIE['dreamteam_page_size'] : $data['page_size'];
							@endphp
							@foreach (config('app.page_size') as $page_size)
								<option value="{!! $page_size !!}" @if ($page_size == $cookie_locale) selected @endif >{!! $page_size !!}</option>
							@endforeach
						</select>
					@endif
					@if (Request()->trash == true)
						<div class="box-action">
							<select class="btn btn-default btn-sm" data-action data-field="{!! $data['action']['field_name'] !!}" style="float: left;height: 30px;margin-right: 5px;">
									<option value="-2">{!! __('Translate::table.batch_process') !!}</option>
									<option value="1">{!! __('Core::admin.general.restore') !!}</option>
									<option value="-3">{!! __('Core::admin.delete_forever') !!}</option>
							</select>
							<button style="float: left;padding: 5px 10px;margin-top: 1px;margin-right: 5px;"
								type="button"
								class="btn btn-sm btn-primary"
								data-table="{{$table_name??''}}"
								data-action_all
								data-warning="{{ __('Core::admin.please_select_action') }}"
								data-message="{{ __('Translate::table.delete_forever_question') }}"
								data-null="{{ __('Core::admin.no_data_delete') }}"
							> {!! __('Translate::table.apply') !!}</button>
						</div>
                    @else
						@if(isset($data['action']) && is_array($data['action']))
							<div class="box-action">
								<select class="btn btn-default btn-sm" data-action data-field="{!! $data['action']['field_name'] !!}" style="float: left;height: 30px;margin-right: 5px;">
									@foreach($data['action']['value'] as $key => $value)
										<option value="{!! $value !!}">{!! __($data['action']['label'][$key]) !!}</option>
									@endforeach
								</select>
								<button style="float: left;padding: 5px 10px;margin-top: 1px;margin-right: 5px;"
									type="button"
									class="btn btn-sm btn-primary"
									data-table="{{$table_name??''}}"
									data-action_all
									data-warning="{{ __('Core::admin.please_select_action') }}"
								> {!! __('Translate::table.apply') !!}</button>
							</div>
						@endif
					@endif
					@if (isset($has_locale) && $has_locale == true && !empty($languages ?? []))
						<select class="btn btn-default btn-sm" data-language_table>
							@php
								$cookie_locale = (isset($_COOKIE['table_locale']))? $_COOKIE['table_locale'] : \App::getLocale();
							@endphp
							@foreach ($languages as $key => $lang)
								<option value="{!! $key !!}" @if ($key == $cookie_locale) selected @endif >{!! $lang['name'] ?? '' !!}</option>
							@endforeach
						</select>
					@endif

					@if (isset($data['no_trash']) && $data['no_trash'] == true)
                    	<a href="{{ route('admin.'.$table_name.'.index', ['trash' => 'true']) }}" class="btn btn-sm btn-default" style="color: #fff;background: #f1b44c;border: none;padding: 5px 20px;">@lang('Translate::table.trash_url')</a>
                    @endif
				</div>
				<div class="float-right pagination-sm m-0">
					{{$data['show_data']->appends(Request()->all())->links()}}
				</div>
				<div class="float-right mr-2">
                    <button type="button" class="btn btn-sm btn-default">@lang('Translate::table.total'): <span class="total">{{$data['show_data']->total()}}</span></button>
				</div>
			</div>

		</div>
	</div>
</div>
@if (isset($include_view_bottom) && !empty($include_view_bottom))
	@foreach ($include_view_bottom as $include_view => $include_data)
		@include($include_view, $include_data)
	@endforeach
@endif

@endsection

@section('foot')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            $('.search-column-selector').on('change', function () {
                const value = $(this).val();
                const parent = $(this).closest('.search-multiple-columns');
                parent.find('.search-value').addClass('d-none').val('');
                parent.find(`.search-value[name="${value}"]`).removeClass('d-none');
            });
            $('.search-column-selector').trigger('change');
        });
    </script>
@endsection
