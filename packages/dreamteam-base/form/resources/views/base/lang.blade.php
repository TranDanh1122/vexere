@if (isset($table_name) && !empty($table_name))
	@if (isset($has_locale) && $has_locale == true)
		@php
			$lang_table = $table_name;
			$lang_referer = Request()->lang_referer;
			$lang_locale = Request()->lang_locale;
		@endphp
		@if (isset($lang_referer) && !empty($lang_referer))
			{{-- Bản ghi hiện tại --}}
			@php $record = DB::table($lang_table)->where('id', $lang_referer)->first(); @endphp
			{{-- Tên bản ghi --}}
			@include('Form::base.disable', [
		        'value'             => $record->name ?? '',
		        'label'             => __('Translate::admin.recore_origin'),
		    ])
			{{-- Ngôn ngữ ẩn --}}
			@include('Form::base.hidden', [
		    	'name' => 'lang_locale',
				'value' => $lang_locale,
		    ])
			{{-- ID bản ghi gốc --}}
		    @include('Form::base.hidden', [
		    	'name' => 'lang_referer',
				'value' => $lang_referer,
		    ])
		@endif
	@endif
@endif