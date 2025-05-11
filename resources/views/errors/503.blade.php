<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>{{ __('Maintenance mode') }}</title>
</head>
<body>
<div class="container">
	@if(function_exists('get_maintenance_setting') && !empty(get_maintenance_setting('description')))
		{!! ThemeManager::renderFont() !!}
    	{!! ThemeManager::renderHeaderStyle() !!}
    	<style type="text/css">
	        @php
	            if (\File::exists(public_path('/assets/general/build/css/desktop/theme/general.min.css'))) {
	                echo BaseHelper::getFileData(public_path('/assets/general/build/css/desktop/theme/general.min.css'), false);
	            }
	        @endphp
	    </style>
		<div class="ck-content">
			{!! get_maintenance_setting('description') !!}
		</div>
	@else
	    <h1>{{ __('Maintenance mode') }}</h1>
	    <p>{{ __('Sorry, we are doing some maintenance. Please check back soon.') }}</p>
	@endif
</div>
</body>
</html>

