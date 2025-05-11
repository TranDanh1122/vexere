@extends('AdminUser::emails.layouts')
@section('content')

<div class="container">
	<div class="chanhtuoi-email">
		<div class="css-content">
			<h3>@lang('AdminUser::admin.email.hello') {{$data['emails']}},</h3>
			<p>@lang('AdminUser::admin.email.note_forgot')</p>
			<div class="text-center">
				<a href="{{$data['links']}}" class="btn btn-primary">@lang('AdminUser::admin.login.reset_password')</a>
			</div>
			<p>@lang('AdminUser::admin.email.note_no_reset_password')</p>
			<p>@lang('AdminUser::admin.email.note_reset_password') <a href="{{$data['links']}}">{{$data['links']}}</a></p>
			<p>@lang('AdminUser::admin.email.sincerely'),</p>
			<p><b>@lang('DreamTeam Team')</b></p>
		</div>
	</div>
</div>

@endsection