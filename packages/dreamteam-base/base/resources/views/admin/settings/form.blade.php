@php
	$breadcrumbs[] = ['name' => $module_name];
@endphp
@extends('Core::layouts.app')

@section('title') @lang($module_name??'') @endsection
@section('content')

<form action="" class="form-horizontal" enctype="multipart/form-data" method="post">
	<div class="row">
		@if (isset($hasLocale) && $hasLocale == true)
			<div class="col-md-12">@include('Translate::partials.admin-list-language-chooser')</div>
		@endif
		@include('Form::generate')
	</div>
</form>

@endsection