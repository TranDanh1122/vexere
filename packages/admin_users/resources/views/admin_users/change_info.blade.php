@extends('Core::layouts.app')

@section('title') @lang($action_name ?? '') @endsection

@section('content')

<form action="{!! route('admin.'.$table_name.'.'.$action, Auth::guard('admin')->user()->id) !!}" class="form-horizontal" enctype="multipart/form-data" method="post">
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<p class="card-title-desc">{{__('Core::admin.general.infor_require')}}</p>
				@include('Form::generate')
			</div>
		</div>
	</div>
	@if (isset($has_seo) && $has_seo == true)
		@include('Form::metaseo', [
			'type' 			=> $table_name,
			'type_id' 		=> $id
		])
	@endif
</div>
</form>

@endsection