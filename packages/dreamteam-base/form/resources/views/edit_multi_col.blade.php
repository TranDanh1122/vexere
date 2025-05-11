@extends('Core::layouts.app')

@section('title') @lang('Translate::form.edit') @lang($module_name) @endsection
@section('content')

<form action="{!! route('admin.'.$table_name.'.update', $id) !!}" class="form-horizontal" enctype="multipart/form-data" method="post">
	<div class="row">
		{{method_field('PUT')}}
		<p class="card-title-desc">{{__('Core::admin.general.infor_require')}}</p>
		@include('Form::generate')
		@if(defined('FILTER_RENDER_SEO_CHECKER'))
			{!! apply_filters(FILTER_RENDER_SEO_CHECKER, null, $table_name, $data_edit ?? (object) []) !!}
		@endif
		@if (isset($has_seo) && $has_seo == true)
			@include('Form::metaseo', [
				'type' 			=> $table_name,
				'type_id' 		=> $id
			])
		@endif
	</div>
</form>

@endsection