@php
	$languages = config('app.language');
	$defaultLang = $languages[config('app.fallback_locale')];
	unset($languages[config('app.fallback_locale')]);
	$languages = array_merge([config('app.fallback_locale') => $defaultLang], $languages);
	// Nếu như có đa ngôn ngữ
	if (isset($has_locale) && $has_locale == true && !empty($languages ?? [])) {
		// Lấy ra mảng lang_code
		$lang_code_array = [];
		foreach ($data['show_data'] as $show_data) {
			$show_data = json_decode(json_encode($show_data));
			$lang_code_array[] = $show_data->lang_code ?? 0;
		}
		// Lấy ra toàn bộ bản ghi đa ngôn ngữ của dữ liệu có trong bảng
		$lang_metas = collect(DB::table('language_metas')->where('lang_table', $table_name)->whereIn('lang_code', $lang_code_array)->get());
	}
	$stt = 0;
@endphp
@foreach ($data['show_data'] as $key => $value)
	@php
		$value = json_decode(json_encode($value));
	@endphp
	<tr data-table="{{$table_name??''}}" data-id="{!! $value->id !!}">
		<td class="text-center">{{++$stt}}</td>
		@if (isset($data['action']) && !empty($data['action']))
			<td class="table-checkbox center" style="width: 50px;">
				<input type="checkbox" class="btn-checkbox checkone">
			</td>
		@endif

		@include($data['view'])

		@foreach ($data['table_generate'] as $generate)
			@switch($generate['type'])
				@case('pin')
	            	@php
	            		$pin_field = $generate['field'];
	            		$pin_name = 'pin_'.$generate['field'];
	            	@endphp
	                @include('Table::components.pin', [
	                	'name' => $generate['field'] ?? '',
	                	'value' => (isset($value->$pin_name) && $value->$pin_name != 2147483647) ? $value->$pin_name : '',
	                	'placeholder' => __('Core::admin.menu.location_title').' vd: 1'
	                ])
                @break
				@case('time')
		        	@include('Table::components.time')
		        @break
				@case('status')
					<td class="form-switch form-switch-lg text-center" style="width: 100px;">
				        <input type="checkbox" class="form-check-input" name="status" value="{!! $value->status !!}" @if($value->status == 1) checked @endif style="padding: 0;margin: 0;left: 0;">
				@break
			    @case('show')
		        	@include('Table::components.action',['type' => 'show'])
		        @break
		        @case('lang')
		        	@if (isset($has_locale) && $has_locale == true && !empty($languages ?? []) && count($languages) > 1)
		        		<td class="lang">
		        			@foreach ($languages as $key => $lang)
		        				@php
		        					$lang_data = $lang_metas->where('lang_locale', $key)->where('lang_code', $value->lang_code)->first();
		        				@endphp
		        				@if (isset($lang_data))
		        					<span><a href="{{ route('admin.'.$table_name.'.edit', $lang_data->lang_table_id) }}"><i class="fas fa-check text-pink"></i></a></span>
		        				@else
		        					<span><a href="{{ route('admin.'.$table_name.'.create', [
		        						'lang_referer' 	=> $value->id,
		        						'lang_locale' 	=> $key,
		        					]) }}"><i class="fas fa-plus-square text-pink"></i></a></span>
		        				@endif
		        			@endforeach
		        		</td>
		        	@endif
		        @break
		        @case('order')
			        @include('Table::components.edit_text', [ 'width' => '100px', 'name' => 'order' ])
		        @break
		        @case('edit')
			        @include('Table::components.action',['type' => 'edit'])
		        @break
		        @case('delete')
			        @include('Table::components.action',['type' => 'delete'])
		        @break
		        @case('delete_custom')
			        @include('Table::components.action',['type' => 'delete_custom'])
		        @break
		        @case('restore')
			        @include('Table::components.action',['type' => 'restore'])
		        @break
		        @case('action')
			        @include('Table::components.action',['type' => 'action'])
		        @break
                @case('action_delete_custom')
                    @include('Table::components.action',['type' => 'action_delete_custom'])
                @break
			@endswitch
		@endforeach
	</tr>
@endforeach

@if (count($data['show_data']) == 0)
	<tr>
		<td colspan="{{count($data['table_generate'])+2}}" class="text-center">@lang('Translate::table.no_record')</td>
	</tr>
@endif
