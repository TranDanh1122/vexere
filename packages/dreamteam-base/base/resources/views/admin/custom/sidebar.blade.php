<div class="settings-sidebar">
	<ul>
		<li @if($setting_name == 'overview') class="active" @endif><a href="{!! route('admin.settings.overview') !!}">{{__('Core::admin.admin_menu.summary')}}</a></li>
		<li @if($setting_name == 'email') class="active" @endif><a href="{!! route('admin.settings.email') !!}">@lang('Email')</a></li>
		<li @if($setting_name == 'code') class="active" @endif><a href="{!! route('admin.settings.code') !!}">{{__('Core::admin.admin_menu.code')}}</a></li>
		<li @if($setting_name == 'call_to_action') class="active" @endif><a href="{!! route('admin.settings.call_to_action') !!}">{{__('Theme::admin.setting.call-to-action')}}</a></li>
	</ul>
</div>