@if (count($errors) > 0)
	<div class="alert alert-danger alert-dismissible" close-alert>
		<a href="#" class="close">×</a>
		@foreach ($errors->all() as $error)
			<p class="mb-0">@lang($error??'')</p>
		@endforeach
	</div>
@endif
@if(Session::has('type'))
	<div class="alert alert-{!! Session::get('type') !!} alert-dismissible" close-alert>
		<a href="#" class="close">×</a>
		<p class="mb-0">@lang(Session::get('message'))</p>
	</div>
@endif