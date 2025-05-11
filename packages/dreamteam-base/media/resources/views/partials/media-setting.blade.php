@extends('Core::layouts.app')

@section('title') @lang($module_name??'') @endsection
@section('content')

<form action="" class="form-horizontal" enctype="multipart/form-data" method="post">
	<div class="row">
		@include('Form::generate')
	</div>
</form>
@include('media::partials.media-generate-thumbnail')
@endsection