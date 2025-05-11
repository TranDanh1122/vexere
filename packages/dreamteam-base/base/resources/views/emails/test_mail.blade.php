@extends('Core::emails.layouts')
@section('content')

<div class="container">
	<div class="chanhtuoi-email">
		<div class="css-content">
			<h3>@lang('AdminUser::admin.email.hello') {{$email ?? ''}},</h3>
			<p>@lang('AdminUser::admin.email.test_config_email')</p>
			<p>@lang('AdminUser::admin.email.bypass')</p>
			<p>@lang('AdminUser::admin.email.sincerely'),</p>
			<p><b>@lang('DreamTeam Team')</b></p>
		</div>
	</div>
</div>

@endsection
